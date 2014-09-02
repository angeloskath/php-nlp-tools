<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\DistanceInterface;

/**
 * Interface describing a spatial index to be used for fast spatial
 * queries like kNearestNeighbors or regionQuery. This interface allows no
 * editing of the index and this is a guarantee that could be limiting in some
 * situations or allow for optimization in other.
 */
interface SpatialIndexInterface
{
    /**
     * Set the distance metric to be used with any operation on this index.
     *
     * @param DistanceInterface $d The distance metric to be used with any operation on this index
     */
    public function setDistanceMetric(DistanceInterface $d);

    /**
     * Index the given points with the given distance metric.
     *
     * @param array $docs The points to be indexed
     */
    public function index(array &$docs);

    /**
     * Return the indices of the datapoints that are within the e-neighborhood
     * of the data point $d.
     *
     * @param  mixed $doc The data point whose neighbors we are looking for
     * @param  float $e   The neighborhood
     * @return array An array of indices that are the neighbors of point $d
     */
    public function regionQuery($doc, $e);

    /**
     * Return the indices of the $k datapoints that are closest to $doc.
     *
     * @param  mixed $doc The data point whose neighbors we are looking for
     * @param  int   $k   How many neighbors do we want
     * @return array An array of indices that are the neighbors of point $d
     */
    public function kNearestNeighbors($doc, $k);
}
