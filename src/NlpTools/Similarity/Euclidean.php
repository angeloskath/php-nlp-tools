<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\FeatureVector;

/**
 * This class computes the very simple euclidean distance between
 * two vectors ( sqrt(sum((a_i-b_i)^2)) ).
 */
class Euclidean implements DistanceInterface
{
    /**
     * See class description
     * @param  FeatureVector $A A feature vector
     * @param  FeatureVector $B Another feature vector
     * @return float         The euclidean distance between $A and $B
     */
    public function dist($A, $B)
    {
        if (!($A instanceof FeatureVector) || !($B instanceof FeatureVector))
            throw new \InvalidArgumentException("Euclidean accepts only FeatureVector instances");

        $r = array();
        foreach ($A as $k=>$v) {
            $r[$k] = $v;
        }
        foreach ($B as $k=>$v) {
            if (isset($r[$k]))
                $r[$k] -= $v;
            else
                $r[$k] = $v;
        }

        return sqrt(
            array_sum(
                array_map(
                    function ($x) {
                        return $x*$x;
                    },
                    $r
                )
            )
        );
    }
}
