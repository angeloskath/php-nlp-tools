<?php

namespace NlpTools\Clustering\CentroidFactories;

use NlpTools\FeatureVector\ArrayFeatureVector;

/**
 * Computes the euclidean centroid of the provided sparse vectors
 */
class Euclidean implements CentroidFactoryInterface
{
    /**
     * Compute the mean value for each dimension.
     *
     * @param  array $docs   The docs from which the centroid will be computed
     * @param  array $choose The indexes from which the centroid will be computed (if empty all the docs will be used)
     * @return mixed The centroid. It could be any form of data a number, a vector (it will be the same as the data provided in docs)
     */
    public function getCentroid(array &$docs, array $choose=array())
    {
        $v = array();
        if (empty($choose)) {
            $choose = range(0, count($docs)-1);
        }
        $cnt = count($choose);
        foreach ($choose as $idx) {
            foreach ($docs[$idx] as $k=>$w) {
                if (!isset($v[$k])) {
                    $v[$k] = $w;
                } else {
                    $v[$k] += $w;
                }
            }
        }
        foreach ($v as &$w) {
            $w /= $cnt;
        }

        return new ArrayFeatureVector($v);
    }
}
