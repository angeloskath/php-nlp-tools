<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * This class computes the very simple euclidean distance between
 * two vectors ( sqrt(sum((a_i-b_i)^2)) ).
 */
class Euclidean implements DistanceInterface
{
    /**
     * see class description
     *
     * @param  array<int|string, mixed> $a Either a vector or a collection of tokens to be transformed to a vector
     * @param  array<int|string, mixed> $b Either a vector or a collection of tokens to be transformed to a vector
     * @return float The euclidean distance between $A and $B
     */
    public function dist(array &$a, array &$b): float
    {
        if (is_int(key($a))) {
            $v1 = array_count_values($a);
        } else {
            $v1 = &$a;
        }

        if (is_int(key($b))) {
            $v2 = array_count_values($b);
        } else {
            $v2 = &$b;
        }

        $r = [];
        foreach ($v1 as $k => $v) {
            $r[$k] = $v;
        }

        foreach ($v2 as $k => $v) {
            if (isset($r[$k])) {
                $r[$k] -= $v;
            } else {
                $r[$k] = $v;
            }
        }

        return sqrt(
            array_sum(
                array_map(
                    fn($x): int|float => $x * $x,
                    $r
                )
            )
        );
    }
}
