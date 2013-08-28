<?php

namespace NlpTools\Clustering;

use NlpTools\Similarity\Neighbors\SpatialIndexInterface;
use NlpTools\Similarity\Neighbors\NaiveLinearSearch;
use NlpTools\Similarity\Distance;
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
    protected $eps;

    /**
     * @param int                   $minPts    The minimum number of points required in the e-neighborhood
     * @param float                 $e         The distance that defines a neighborhood
     * @param Distance              $d         The distance to be used with the SpatialIndex
     * @param SpatialIndexInterface $neighbors An index for efficiently performing distance related queries
     */
    public function __construct($minPts, $e, Distance $d, SpatialIndexInterface $neighbors=null)
    {
        $this->minPts = $minPts;
        $this->eps = $e;
        if ($neighbors === null) {
            $neighbors = new NaiveLinearSearch();
        }
        $this->neighbors = $neighbors;
        $this->neighbors->setDistanceMetric($d);
    }

    /**
     * @param  TrainingSet    $document The documents to be clustered
     * @param  FeatureFactory $ff       A feature factory to transform the documents given
     * @return array          The clusters, an array containing arrays of offsets for the documents
     */
    public function cluster(TrainingSet $tset, FeatureFactory $ff)
    {
        $docs = $this->getDocumentArray($tset, $ff);
        $this->neighbors->index($docs);

        $visited = array_fill_keys(range(0,count($docs)-1), false);
        $clusters = array_fill_keys(range(0,count($docs)-1), -1);
        $c = -1; // current cluster

        // for every data point
        foreach ($docs as $idx=>$d) {
            if ($visited[$idx])
                continue;

            $visited[$idx] = true;
            $neighbors = $this->neighbors->regionQuery($d, $this->eps);

            // we have ourselves a core point
            if (count($neighbors)>=$this->minPts) {
                $c++; // next cluster
                $clusters[$idx] = $c;
                
                // expand cluster $c
                $set = array();
                $iter = new \AppendIterator();
                $iter->append(new \ArrayIterator($neighbors));
                // while we still have neighbors
                foreach ($iter as $i) {
                    $set[$i] = true;
                    // if we haven't visited this point before we
                    // should check it for neighbors
                    if (!$visited[$i]) {
                        $visited[$i] = true;
                        $neighbors = $this->neighbors->regionQuery($docs[$i], $this->eps);
                        if (count($neighbors)>=$this->minPts) {
                            // add only the points that have not been added
                            $iter->append(
                                new \ArrayIterator(
                                    array_filter(
                                        $neighbors,
                                        function ($n) use(&$set) {
                                            return !isset($set[$n]);
                                        }
                                    )
                                )
                            );
                        }
                    }

                    // if it is not member of any other cluster then
                    // it should be of cluster $c
                    if ($clusters[$i]<0) {
                        $clusters[$i] = $c;
                    }
                }
            }
        }

        $actual_clusters = array();
        $noise = array();
        foreach ($clusters as $i=>$c) {
            if ($c<0) {
                $noise[] = $i;
                continue;
            }
            if (!isset($actual_clusters[$c])) {
                $actual_clusters[$c]=array();
            }
            $actual_clusters[$c][] = $i;
        }

        return array($actual_clusters, $noise);
    }
}
