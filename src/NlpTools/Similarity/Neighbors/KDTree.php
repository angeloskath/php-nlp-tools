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
     * The sample size that the median will be calculated with
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
     * A reference to the docs so that we do not copy our data around
     */
    protected $docs_reference;
    /**
     * A boolean that is true when the docs are in the form key=>value and
     * false if they are in the form key1, key1, key2, key3, etc
     */
    protected $docs_is_map;
    /**
     * Our vocabulary that contains the axes that we have in this tree and the
     * order in which we traverse them
     */
    protected $vocabulary;

    /**
     * KDTree only accepts EuclideanDistance instances as distance metrics.
     *
     * @throws InvalidArgumentException if $d is not an EuclideanDistance
     * @param  DistanceInterface        $d
     */
    public function setDistanceMetric(DistanceInterface $d)
    {
        if (!($d instanceof Euclidean))
            throw new \InvalidArgumentException();

        $this->dist = $d;
    }

    /**
     * {@inheritdoc}
     */
    public function index(array &$docs)
    {
        // hold a reference to the docs so we will only be keeping integers in
        // our tree and we might reduce a bit the memory overhead of the tree.
        $this->docs_reference = &$docs;

        // find out if we are given a map or a set of keys without count
        $this->docs_is_map = !is_int(key($docs[0]));

        // generate 
        $this->generateVocabulary($docs);

        // recursively build the tree
        $this->tree = $this->buildTree(0, range(0, count($docs)-1));
    }

    /**
     * {@inheritdoc}
     */
    public function regionQuery($doc, $e)
    {
        $nn = array();
        $docs_reference = &$this->docs_reference;
        $dist = $this->dist;
        $this->traverseTree(
            $doc,
            function () use($e) { return $e; },
            function ($docIdx) use(&$nn, &$docs_reference, &$doc, $dist, $e) {
                if ($dist->dist($doc, $docs_reference[$docIdx]) <= $e)
                    $nn[] = $docIdx;
            }
        );

        return $nn;
    }

    /**
     * {@inheritdoc}
     */
    public function kNearestNeighbors($doc, $k)
    {
        if ($k > count($this->docs_reference))
            return range(0, count($this->docs_reference) - 1);

        $nn = array_fill(0, $k, null);
        $nnD = array_fill(0, $k, INF);
        $dist = $this->dist;
        $docs_reference = &$this->docs_reference;

        $this->traverseTree(
            $doc,
            function () use(&$nnD) {
                return end($nnD);
            },
            function ($docIdx) use(&$nn, &$nnD, &$docs_reference, &$doc, $dist) {
                $d = $dist->dist(
                    $doc,
                    $docs_reference[$docIdx]
                );

                // ok this is nearest than the last neighbor insert it to the
                // list
                if ($d < end($nnD)) {
                    $idx = 0;
                    foreach ($nnD as $distance) {
                        if ($distance < $d)
                            $idx++;
                        else
                            break;
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
    protected function generateVocabulary(array &$docs)
    {
        $this->vocabulary = array();
        foreach ($docs as $doc) {
            // add them in the set
            if ($this->docs_is_map) {
                foreach ($doc as $axis=>$value) {
                    $this->vocabulary[$axis] = 1;
                }
            } else {
                foreach ($doc as $axis) {
                    $this->vocabulary[$axis] = 1;
                }
            }
        }

        // flip the vocabulary to its keys so it now contains all the axis
        // found in the set of documents
        // NOTE: Consider making this an SplFixedArray
        $this->vocabulary = array_keys($this->vocabulary);
    }

    private function valueOfDocAtDepth($docIdx, $depth)
    {
        $key = $this->vocabulary[$depth % count($this->vocabulary)];

        if ($this->docs_is_map) {
            if (isset($this->docs_reference[$docIdx][$key]))
                return $this->docs_reference[$docIdx][$key];
            else
                return 0;
        } else {
            $v = 0;
            foreach ($this->docs_reference[$docIdx] as $axis)
                $v += (int)($axis == $key);

            return $v;
        }
    }

    private function valueOfFullDocAtDepth($doc, $depth)
    {
        $key = $this->vocabulary[$depth % count($this->vocabulary)];

        if (is_int(key($doc))) {
            $v = 0; 
            foreach ($doc as $axis)
                $v += (int)($axis == $key);

            return $v;
        } else {
            return (isset($doc[$key])) ? $doc[$key] : 0;
        }
    }

    protected function plane($depth, $value, &$doc)
    {
        $plane = null;
        if (is_int(key($doc)))
            $plane = array_count_values($doc);
        else
            $plane = $doc;
        $plane[$this->vocabulary[$depth % count($this->vocabulary)]] = $value;

        return $plane;
    }

    protected function buildTree($depth, $docs)
    {
        if (count($docs)==0) {
            return null;
        }

        if (count($docs)==1) {
            $node = new \stdClass;
            $node->value = $docs[0];
            $node->left = null;
            $node->right = null;
            return $node;
        }

        // pick 50 random docs to calculate an estimate of the median of docs
        // at this axis
        $random_picks = array();
        if (count($docs) < self::SAMPLE_SIZE) {
            $random_picks = range(0, count($docs)-1);
        } else {
            $random_picks = array_rand($docs, self::SAMPLE_SIZE);
        }

        // get the value of the 50 docs
        $values = array();
        foreach ($random_picks as $doc) {
            $values[] = $this->valueOfDocAtDepth($docs[$doc], $depth);
        }
        sort($values);

        // this is our median
        $median = $values[(int)(count($values)/2)-1];

        // create two sets of docs the ones above and the ones below the median
        $leftDocs = array();
        $rightDocs = array();

        // this is a special case where all the values might be equal so split
        // the docs in half 
        if ($median == reset($values) && $median == end($values)) {
            $cnt = 0;
            foreach ($docs as $docIdx) {
                if ($this->valueOfDocAtDepth($docIdx, $depth) != $median)
                    break;
                $cnt ++;
            }
            if ($cnt == count($docs)) {
                $leftDocs = array_slice($docs, 0, (int)(count($docs)/2));
                $rightDocs = array_slice($docs, (int)(count($docs)/2));
            }
        }

        // we need to split to the median
        if (empty($leftDocs)) {
            foreach ($docs as $docIdx) {
                if ($this->valueOfDocAtDepth($docIdx, $depth) <= $median)
                    $leftDocs[] = $docIdx;
                else
                    $rightDocs[] = $docIdx;
            }
        }

        // create the node
        $node = new \stdClass;
        $node->value = $median;
        $node->left = $this->buildTree($depth + 1, $leftDocs);
        $node->right = $this->buildTree($depth + 1, $rightDocs);

        return $node;
    }

    protected function traverseTree(&$doc, $minDistance, $pointNode)
    {
        // create a stack for depth first traversal
        $stack = array(array($this->tree, 0));

        while (!empty($stack)) {
            list($node, $depth) = array_pop($stack);

            // splitting line, see if we are going to add all the children
            // to the search
            if ($node->left!==null || $node->right!==null) {
                $d = $this->valueOfFullDocAtDepth($doc, $depth) - $node->value;

                // ask the minDistance function whether we should be adding both or not
                if (abs($d) < call_user_func($minDistance) || $d == 0) {
                    // we 'll add both
                    if ($node->left!==null)
                        array_push($stack, array($node->left, $depth + 1));
                    if ($node->right!==null)
                        array_push($stack, array($node->right, $depth + 1));
                } else {
                    // we 'll only add one
                    if ($d > 0) {
                        if ($node->right!==null)
                            array_push($stack, array($node->right, $depth + 1));
                    } else {
                        if ($node->left!==null)
                            array_push($stack, array($node->left, $depth + 1));
                    }
                }
            } else {
                // this is a point so call the appropriate function
                call_user_func($pointNode, $node->value);
            }
        }
    }
}
