<?php

namespace NlpTools\Clustering;

use NlpTools\Similarity\DistanceInterface;
use NlpTools\Clustering\CentroidFactories\CentroidFactoryInterface;
use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\FeatureFactoryInterface;

/**
 * This clusterer uses the KMeans algorithm for clustering documents.
 * It accepts as parameters the number of clusters and the distance metric
 * as well as the methodology for computing the new centroids (thus it
 * can be used to cluster documents in spaces other than the euclidean
 * vector space).
 * A description of this algorithm can be found at
 * http://en.wikipedia.org/wiki/K-means_clustering
 */
class KMeans extends Clusterer
{
    protected $dist;
    protected $centroidF;
    protected $n;
    protected $cutoff;

    /**
     * Initialize the K Means clusterer
     *
     * @param int                      $n      The number of clusters to compute
     * @param DistanceInterface        $d      The distance metric to be used (Euclidean, Hamming, ...)
     * @param CentroidFactoryInterface $cf     This parameter will be used to create the new centroids from a set of documents
     * @param float                    $cutoff When the maximum change of the centroids is smaller than that stop iterating
     */
    public function __construct($n, DistanceInterface $d, CentroidFactoryInterface $cf, $cutoff=1e-5)
    {
        $this->dist = $d;
        $this->n = $n;
        $this->cutoff = $cutoff;
        $this->centroidF = $cf;
    }

    /**
     * Apply the feature factory to the documents and then cluster the resulting array
     * using the provided distance metric and centroid factory.
     */
    public function cluster(TrainingSet $documents, FeatureFactoryInterface $ff)
    {
        // transform the documents according to the FeatureFactory
        $docs = $this->getDocumentArray($documents,$ff);

        // choose N centroids at random
        $centroids = array();
        foreach (array_rand($docs,$this->n) as $key) {
            $centroids[] = $docs[$key];
        }

        // cache the distance and centroid factory functions for use
        // with closures
        $dist = array($this->dist,'dist');
        $cf = array($this->centroidF,'getCentroid');

        // looooooooop
        while (true) {
            // compute the distance each document has from our centroids
            // the array is MxN where M = count($docs) and N = count($centroids)
            $distances = array_map(
                function ($doc) use (&$centroids,$dist) {
                    return array_map(
                        function ($c) use ($dist,$doc) {
                            // it is passed with an array because dist expects references
                            // and it failed when run with phpunit.
                            // see http://php.net/manual/en/function.call-user-func.php
                            // for the solution used below
                            return call_user_func_array(
                                $dist,
                                array(
                                    &$c,
                                    &$doc
                                )
                            );
                        },
                        $centroids
                    );
                },
                $docs
            );

            // initialize the empty clusters
            $clusters = array_fill_keys(
                array_keys($centroids),
                array()
            );
            foreach ($distances as $idx=>$d) {
                // assign document idx to the closest centroid
                $clusters[array_search(min($d),$d)][] = $idx;
            }

            // compute the new centroids from the assigned documents
            // using the centroid factory function
            $new_centroids = array_map(
                function ($cluster) use (&$docs,$cf) {
                    return call_user_func_array(
                        $cf,
                        array(
                            &$docs,
                            $cluster
                        )
                    );
                },
                $clusters
            );

            // compute the change each centroid had from the previous one
            $changes = array_map(
                $dist,
                $new_centroids,
                $centroids
            );

            // if the largest change is small enough we are done
            if (max($changes)<$this->cutoff) {
                // return the clusters, the centroids and the distances
                return array($clusters,$centroids,$distances);
            }

            // update the centroids and loooooop again
            $centroids = $new_centroids;
        }
    }
}
