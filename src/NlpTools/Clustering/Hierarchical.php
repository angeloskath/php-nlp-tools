<?php

namespace NlpTools\Clustering;

use NlpTools\Clustering\MergeStrategies\MergeStrategyInterface;
use NlpTools\Similarity\DistanceInterface;
use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\FeatureFactoryInterface;

/**
 * This class implements hierarchical agglomerative clustering.
 * It receives a MergeStrategy as a parameter and a Distance metric.
 */
class Hierarchical extends Clusterer
{
    protected $strategy;
    protected $dist;

    public function __construct(MergeStrategyInterface $ms, DistanceInterface $d)
    {
        $this->strategy = $ms;
        $this->dist = $d;
    }

    /**
     * Iteratively merge documents together to create an hierarchy of clusters.
     * While hierarchical clustering only returns one element, it still wraps it
     * in an array to be consistent with the rest of the clustering methods.
     *
     * @return array An array containing one element which is the resulting dendrogram
     */
    public function cluster(TrainingSet $documents, FeatureFactoryInterface $ff)
    {
        // what a complete waste of memory here ...
        // the same data exists in $documents, $docs and
        // the only useful parts are in $this->strategy
        $docs = $this->getDocumentArray($documents, $ff);
        $this->strategy->initializeStrategy($this->dist,$docs);
        unset($docs); // perhaps save some memory

        // start with all the documents being in their
        // own cluster we 'll merge later
        $clusters = range(0,count($documents)-1);
        $c = count($clusters);
        while ($c>1) {
            // ask the strategy which to merge. The strategy
            // will assume that we will indeed merge the returned clusters
            list($i,$j) = $this->strategy->getNextMerge();
            $clusters[$i] = array($clusters[$i],$clusters[$j]);
            unset($clusters[$j]);
            $c--;
        }
        $clusters = array($clusters[$i]);

        // return the dendrogram
        return array($clusters);
    }

    /**
     * Flatten a dendrogram to an almost specific
     * number of clusters (the closest power of 2 larger than
     * $NC)
     *
     * @param  array   $tree The dendrogram to be flattened
     * @param  integer $NC   The number of clusters to cut to
     * @return array   The flat clusters
     */
    public static function dendrogramToClusters($tree,$NC)
    {
        $clusters = $tree;
        while (count($clusters)<$NC) {
            $tmpc = array();
            foreach ($clusters as $subclust) {
                if (!is_array($subclust))
                    $tmpc[] = $subclust;
                else {
                    foreach ($subclust as $c)
                        $tmpc[] = $c;
                }
            }
            $clusters = $tmpc;
        }
        foreach ($clusters as &$c) {
            $c = iterator_to_array(
                new \RecursiveIteratorIterator(
                    new \RecursiveArrayIterator(
                        array($c)
                    )
                ),
                false // do not use keys
            );
        }

        return $clusters;
    }
}
