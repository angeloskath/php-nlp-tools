<?php

declare(strict_types=1);

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
    public function __construct(protected MergeStrategyInterface $mergeStrategy, protected DistanceInterface $distance)
    {
    }

    /**
     * Iteratively merge documents together to create an hierarchy of clusters.
     * While hierarchical clustering only returns one element, it still wraps it
     * in an array to be consistent with the rest of the clustering methods.
     *
     * @return array<int, mixed> An array containing one element which is the resulting dendrogram
     */
    public function cluster(TrainingSet $trainingSet, FeatureFactoryInterface $featureFactory): array
    {
        // what a complete waste of memory here ...
        // the same data exists in $documents, $docs and
        // the only useful parts are in $this->strategy
        $docs = $this->getDocumentArray($trainingSet, $featureFactory);
        $this->mergeStrategy->initializeStrategy($this->distance, $docs);
        unset($docs); // perhaps save some memory

        // start with all the documents being in their
        // own cluster we 'll merge later
        $clusters = range(0, count($trainingSet) - 1);
        $i = 0;
        $c = count($clusters);
        while ($c > 1) {
            // ask the strategy which to merge. The strategy
            // will assume that we will indeed merge the returned clusters
            [$i, $j] = $this->mergeStrategy->getNextMerge();
            $clusters[$i] = [$clusters[$i], $clusters[$j]];
            unset($clusters[$j]);
            $c--;
        }

        $clusters = [$clusters[$i]];

        // return the dendrogram
        return [$clusters];
    }

    /**
     * Flatten a dendrogram to an almost specific
     * number of clusters (the closest power of 2 larger than
     * $NC)
     *
     * @param  array<int, mixed>   $tree The dendrogram to be flattened
     * @param  integer $numberOfClusters   The number of clusters to cut to
     * @return array<int, mixed>   The flat clusters
     */
    public static function dendrogramToClusters(array $tree, int $numberOfClusters): array
    {
        $clusters = $tree;
        while (count($clusters) < $numberOfClusters) {
            $tmpc = [];
            foreach ($clusters as $cluster) {
                if (!is_array($cluster)) {
                    $tmpc[] = $cluster;
                } else {
                    foreach ($cluster as $c) {
                        $tmpc[] = $c;
                    }
                }
            }

            $clusters = $tmpc;
        }

        foreach ($clusters as &$cluster) {
            $cluster = iterator_to_array(
                new \RecursiveIteratorIterator(
                    new \RecursiveArrayIterator(
                        [$cluster]
                    )
                ),
                false // do not use keys
            );
        }

        return $clusters;
    }
}
