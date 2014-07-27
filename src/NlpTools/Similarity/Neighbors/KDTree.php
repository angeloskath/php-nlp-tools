<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\DistanceInterface;
use NlpTools\Similarity\EuclideanDistance;

/**
 * KDTree is a binary search tree for n dimensional data.
 * 
 * This KDTree implementation only accepts euclidean distance as metric.
 * http://en.wikipedia.org/wiki/K-d_tree
 */
class KDTree
{
    protected $dist;
    protected $tree;
    protected $vocabulary;

    /**
     * KDTree only accepts EuclideanDistance instances as distance metrics.
     *
     * @throws InvalidArgumentException if $d is not an EuclideanDistance
     * @param  DistanceInterface        $d
     */
    public function setDistanceMetric(DistanceInterface $d)
    {
        if (!($d instanceof EuclideanDistance))
            throw new \InvalidArgumentException();

        $this->dist = $d;
    }

    /**
     * {@inheritdoc}
     */
    public function index(array &$docs)
    {
        // generate 
        $this->generateVocabulary($docs);

        // create the tree
        $tree = new stdClass;

    }

    /**
     * Loop through all the docs to generate the vocabulary. The vocabulary is
     * the axis that we are going to cycle through in the KDTree
     */
    protected function generateVocabulary(array &$docs)
    {
        $this->vocabulary = array();
        foreach ($docs as $doc) {
            // make sure we are given the doc as an array of key value pairs
            if (is_int(key($doc))) {
                $doc = array_count_values($doc);
            }

            // add them in the set
            foreach ($doc as $axis=>$value) {
                $this->vocabulary[$axis] = 1;
            }
        }

        // flip the vocabulary to its keys so it now contains all the axis
        // found in the set of documents
        $this->vocabulary = array_keys($this->vocabulary);
    }
}
