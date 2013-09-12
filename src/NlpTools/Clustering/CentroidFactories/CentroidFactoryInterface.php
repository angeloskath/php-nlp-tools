<?php

namespace NlpTools\Clustering\CentroidFactories;

interface CentroidFactoryInterface
{
    /**
     * Parse the provided docs and create a doc that given a metric
     * of distance is the centroid of the provided docs.
     *
     * The second array is to choose some of the provided docs to
     * compute the centroid.
     *
     * @param  array $docs   The docs from which the centroid will be computed
     * @param  array $choose The indexes from which the centroid will be computed (if empty all the docs will be used)
     * @return mixed The centroid. It could be any form of data a number, a vector (it will be the same as the data provided in docs)
     */
    public function getCentroid(array &$docs, array $choose=array());
}
