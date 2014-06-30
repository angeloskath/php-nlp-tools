<?php

namespace NlpTools\Similarity;

/**
 * This class implements the hamming distance of two strings or sets.
 * To be used with numbers one should pass the numbers to decbin() first
 * and make sure the smaller number is properly padded with zeros.
 */
class HammingDistance implements DistanceInterface
{
    /**
     * Count the number of positions that A and B differ.
     *
     * @param  string|array $A
     * @param  string|array $B
     * @return int          The hamming distance of the two strings/sets A and B
     */
    public function dist(&$A, &$B)
    {
        if (is_array($A))
            $l1 = count($A);
        else if (is_string($A))
            $l1 = strlen($A);
        else
            throw new \InvalidArgumentException(
                "HammingDistance accepts only strings or arrays"
            );
        if (is_array($B))
            $l2 = count($B);
        else if (is_string($B))
            $l2 = strlen($B);
        else
            throw new \InvalidArgumentException(
                "HammingDistance accepts only strings or arrays"
            );

        $l = min($l1,$l2);
        $d = 0;
        for ($i=0;$i<$l;$i++) {
            $d += (int) ($A[$i]!=$B[$i]);
        }

        return $d + (int) abs($l1-$l2);
    }
}
