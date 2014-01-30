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
     * @param  string $A
     * @param  string $B
     * @return int    The hamming distance of the two strings A and B
     */
    public function dist(&$A, &$B)
    {
        $l1 = strlen($A);
        $l2 = strlen($B);
        $l = min($l1,$l2);
        $d = 0;
        for ($i=0;$i<$l;$i++) {
            $d += (int) ($A[$i]!=$B[$i]);
        }

        return $d + (int) abs($l1-$l2);
    }
}
