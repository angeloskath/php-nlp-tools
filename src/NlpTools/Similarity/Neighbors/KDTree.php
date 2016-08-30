<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\DistanceInterface;
use NlpTools\Similarity\Euclidean;

/**
 * KDTree is a binary search tree for n dimensional data.
 *
 * This KDTree implementation only accepts euclidean distance as metric.
 * http://en.wikipedia.org/wiki/K-d_tree
 */
class KDTree implements SpatialIndexInterface
{
    /**
     * The amount of documents to use to compute the median of an axis.
     */
    const SAMPLE_SIZE = 50;

    /**
     * An instance of the distance that we are using (always a subclass of
     * Euclidean)
     */
    protected $dist;
    /**
     * Our internal tree
     */
    protected $tree;
    /**
     * Our vocabulary that contains the axes that we have in this tree and the
     * order in which we traverse them
     */
    protected $vocabulary;
    /**
     * A local copy of the documents
     */
    protected $docs;
    /**
     * The number of documents in the leaves
     */
    protected $leafSize;

    public function __construct($leafSize = 100)
    {
        $this->dist = new Euclidean();
        $this->leafSize = $leafSize;
    }

    /**
     * KDTree only accepts EuclideanDistance instances as distance metrics.
     *
     * @throws InvalidArgumentException if $d is not an EuclideanDistance
     * @param  DistanceInterface        $d
     */
    public function setDistanceMetric(DistanceInterface $d)
    {
        if (!($d instanceof Euclidean)) {
            throw new \InvalidArgumentException();
        }

        $this->dist = $d;
    }

    /**
     * {@inheritdoc}
     */
    public function index(array &$docs)
    {
        // copy the docs (the copy is shallow so not to memory intensive)
        $this->docs = $docs;

        // generate
        $this->generateVocabulary();

        // recursively build the tree
        $this->tree = $this->buildTree(0, range(0, count($this->docs)-1));
    }

    /**
     * {@inheritdoc}
     */
    public function regionQuery($doc, $e)
    {
        $nn = array();
        $docs = &$this->docs;
        $dist = $this->dist;
        $this->traverseTree(
            $doc,
            function () use ($e) {
                return $e;
            },
            function ($docIdx) use (&$nn, &$docs, &$doc, $dist, $e) {
                if ($dist->dist($doc, $docs[$docIdx]) <= $e) {
                    $nn[] = $docIdx;
                }
            }
        );

        return $nn;
    }

    /**
     * {@inheritdoc}
     */
    public function kNearestNeighbors($doc, $k)
    {
        if ($k > count($this->docs)) {
            return range(0, count($this->docs) - 1);
        }

        $nn = array_fill(0, $k, null);
        $nnD = array_fill(0, $k, INF);
        $dist = $this->dist;
        $docs = &$this->docs;

        $this->traverseTree(
            $doc,
            function () use (&$nnD) {
                return end($nnD);
            },
            function ($docIdx) use (&$nn, &$nnD, &$docs, &$doc, $dist) {
                $d = $dist->dist(
                    $doc,
                    $docs[$docIdx]
                );

                // ok this is nearest than the last neighbor insert it to the
                // list
                if ($d < end($nnD)) {
                    $idx = 0;
                    foreach ($nnD as $distance) {
                        if ($distance < $d) {
                            $idx++;
                        } else {
                            break;
                        }
                    }
                    array_splice($nn, $idx, 0, $docIdx);
                    array_pop($nn);
                    array_splice($nnD, $idx, 0, $d);
                    array_pop($nnD);
                }
            }
        );

        return $nn;
    }

    /**
     * Loop through all the docs to generate the vocabulary. The vocabulary is
     * the axis that we are going to cycle through in the KDTree
     */
    private function generateVocabulary()
    {
        $this->vocabulary = array();
        foreach ($this->docs as $doc) {
            // add them in the set
            foreach ($doc as $axis=>$value) {
                $this->vocabulary[$axis] = 1;
            }
        }

        // flip the vocabulary to its keys so it now contains all the axis
        // found in the set of documents
        // NOTE: Consider making this an SplFixedArray
        $this->vocabulary = array_keys($this->vocabulary);
    }

    /**
     * Return the value of a document at a specific depth which means find the
     * axis and return its value.
     */
    private function valueOfDocAtDepth($depth, $doc)
    {
        $key = $this->vocabulary[$depth % count($this->vocabulary)];

        return isset($doc[$key]) ? $doc[$key] : 0;
    }

    /**
     * Estimate the median by examining SAMPLE_SIZE random documents.
     *
     * Although we could use reservoir sampling or another sampling method we
     * don't care for examining exactly SAMPLE_SIZE documents so we use a
     * simpler alternative.
     */
    private function estimateMedian($depth, $docs)
    {
        // To hold the values that we need to sort and choose the median from
        $values = array();

        // Collect the values from each document with
        // probability SAMPLE_SIZE/$N
        $N = count($docs);
        foreach ($docs as $doc) {
            if (mt_rand(0, $N) <= self::SAMPLE_SIZE) {
                $values[] = $this->valueOfDocAtDepth($depth, $this->docs[$doc]);
            }
        }

        // Return the median
        sort($values);

        return $values[(int)((count($values)-1)/2)];
    }

    /**
     * Build the tree recursively.
     */
    private function buildTree($depth, $docs)
    {
        if (count($docs)==0) {
            return null;
        }

        if (count($docs) <= $this->leafSize) {
            $node = new \stdClass;
            $node->value = $docs;
            $node->left = null;
            $node->right = null;
            return $node;
        }

        // estimate a splitting point (an approximation of the median which is
        // optimal) at this axis and depth
        $median = $this->estimateMedian($depth, $docs);

        // create two sets of docs the ones above and the ones below the median
        $leftDocs = array();
        $rightDocs = array();

        // we need to split to the median
        foreach ($docs as $doc) {
            if ($this->valueOfDocAtDepth($depth, $this->docs[$doc]) <= $median) {
                $leftDocs[] = $doc;
            } else {
                $rightDocs[] = $doc;
            }
        }

        // create the node
        $node = new \stdClass;
        $node->value = $median;
        $node->left = $this->buildTree($depth + 1, $leftDocs);
        $node->right = $this->buildTree($depth + 1, $rightDocs);

        return $node;
    }

    /**
     * Traverse the tree in depth first order (and close to the passed in doc)
     * and then when unfolding the stack to traverse the rest of the tree prune
     * branches according to the value returned by the $minDistance function.
     * Each point that is accessed is passed to the $pointNode function.
     */
    protected function traverseTree($doc, $minDistance, $pointNode)
    {
        // create a stack for depth first traversal
        $stack = array(array($this->tree, 0));

        while (!empty($stack)) {
            list($node, $depth) = array_pop($stack);

            if ($node === null) {
                continue;
            }

            // If it is a point node examine it by calling $pointNode.
            if (is_array($node->value)) {
                foreach ($node->value as $leaf) {
                    call_user_func($pointNode, $leaf);
                }
            } else {
                // This node splits the dataset with a plane. This plane is
                // $distance far away from our $doc. We must check stuff that
                // are less than $minDistanceValue away.
                $distance = $this->valueOfDocAtDepth($depth, $doc) - $node->value;
                $minDistanceValue = call_user_func($minDistance);
                $left = array($node->left, $depth+1);
                $right = array($node->right, $depth+1);

                // The plane is closer than the minDistanceValue so we cannot
                // discard anything we need to check both sides of the plane
                if (abs($distance) <= $minDistanceValue) {
                    // But we will first check the side which is closest to our
                    // point. If the distance is negative then that means that
                    // the point resides to the left of the plane otherwise to
                    // the right.
                    if ($distance <= 0) {
                        array_push($stack, $right);
                        array_push($stack, $left);
                    } else {
                        array_push($stack, $left);
                        array_push($stack, $right);
                    }
                } elseif ($distance <= 0) {
                    array_push($stack, $left);
                } else {
                    array_push($stack, $right);
                }
            }
        }
    }
}
