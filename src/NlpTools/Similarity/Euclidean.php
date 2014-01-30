<?php

namespace NlpTools\Similarity;

/**
 * This class computes the very simple euclidean distance between
 * two vectors ( sqrt(sum((a_i-b_i)^2)) ).
 */
class Euclidean implements DistanceInterface
{
    /**
     * see class description
     * @param  array $A Either a vector or a collection of tokens to be transformed to a vector
     * @param  array $B Either a vector or a collection of tokens to be transformed to a vector
     * @return float The euclidean distance between $A and $B
     */
    public function dist(&$A, &$B)
    {
        if (is_int(key($A)))
            $v1 = array_count_values($A);
        else
            $v1 = &$A;
        if (is_int(key($B)))
            $v2 = array_count_values($B);
        else
            $v2 = &$B;

        $r = array();
        foreach ($v1 as $k=>$v) {
            $r[$k] = $v;
        }
        foreach ($v2 as $k=>$v) {
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
