<?php

namespace NlpTools\Clustering;

use NlpTools\Similarity\Neighbors\SpatialIndex;
use NlpTools\FeatureFactories\FeatureFactory;
use NlpTools\Documents\TrainingSet;

/**
 * DBSCAN is a density based clustering algorithm.
 * http://en.wikipedia.org/wiki/DBSCAN
 */
class Dbscan extends Clusterer
{

    protected $neighbors;
    protected $minPts;
    protected $e;

    /**
     * @param SpatialIndex $neighbors An index for efficiently performing distance related queries
     * @param int          $minPts    The minimum number of points required in the e-neighborhood
     * @param float        $e         The distance that defines a neighborhood
     */
    public function __construct(SpatialIndex $neighbors, $minPts, $e)
    {
        $this->neighbors = $neighbors;
        $this->minPts = $minPts;
        $this->e = $e;
    }

    /**
     * @param  TrainingSet    $document The documents to be clustered
     * @param  FeatureFactory $ff       A feature factory to transform the documents given
     * @return array          The clusters, an array containing arrays of offsets for the documents
     */
    public function cluster(TrainingSet $tset, FeatureFactory $ff)
    {
        $docs = $this->getDocumentArray();

        $noise = array();
        $visited = array();
        $clusters = array_fill_keys(range(0,count($docs)),-1);
        $c = -1; // current cluster

        // for every data point
        foreach ($docs as $idx=>$d) {
            $visited[$i] = true;
            $idxs = $this->neighbors->regionQuery($idx, $this->eps);

            // if it has a few neighbors then it is noise
            if (count($idxs)<$this->minPts) {
                $noise[] = $i;
            } else {
                // we have found a new cluster increment $c
                $c++;
                $clusters[$i] = $c;
                $l = count($idxs);
                // foreach neighbors of $i
                for ($j=0;$j<$l;$j++) {
                    // new $i the previous $i's neighbor
                    $i = $idxs[$j];
                    if (!isset($visited[$i])) {
                        // we haven't visited before so we need to
                        // find its neighbors
                        $visited[$i] = true;
                        $new_idxs = $this->neighbors->regionQuery($docs[$i], $this->eps);
                        // if it has the required density
                        // (sufficient amounf of neighbors in sufficiently small distance)
                        // also add its neighbors to the cluster
                        if (count($new_idxs)>=$this->minPts) {
                            $idxs = array_merge($idxs,$new_idxs);
                            $l = count($idxs);
                        }
                    }
                    if (!isset($clusters[$i])) {
                        // add the neighbor to the cluster if it
                        // doesn't belong to any other cluster already
                        $clusters[$i] = $c;
                    }
                }
            }
        }

        $actual_clusters = array();
        foreach ($clusters as $i=>$c) {
            if (!isset($actual_clusters[$c])) {
                $actual_clusters[$c]=array();
            }
            $actual_clusters[$c][] = $i;
        }

        return array($actual_clusters, $noise);
    }
}
