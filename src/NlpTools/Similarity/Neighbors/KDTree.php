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
        $this->docs_is_map = is_int(key($docs[0]));

        // generate 
        $this->generateVocabulary($docs);

        // recursively build the tree
        $this->tree = $this->buildTree();
    }

    /**
     * {@inheritdoc}
     */
    public function regionQuery($doc, $e)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function kNearestNeighbors($doc, $k)
    {
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

    protected function valueOfDocAtDepth($docIdx, $depth)
    {
        $key = $this->vocabulary[$depth % count($this->vocabulary)];

        if ($this->docs_is_map) {
            if (isset($this->docs_reference[$docIdx][$key]))
                return $this->docs_reference[$docIdx][$key];
            else
                return 0;
        } else {
            $v = 0;
            foreach ($this->docs_reference[$docIdx] as $axis) {
                $v += (int)($axis == $key);
            }
            return $v;
        }
    }

    protected function buildTree()
    {
        
    }
}
