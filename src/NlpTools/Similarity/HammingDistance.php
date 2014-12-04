<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\FeatureVector;

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
     * @param  string|FeatureVector $A
     * @param  string|FeatureVector $B
     * @return int                  The hamming distance of the two
     *                              strings/sets A and B
     */
    public function dist($A, $B)
    {
        if (is_string($A) && is_string($B)) {
            return $this->hammingOfStrings($A, $B);
        } elseif ($A instanceof FeatureVector && $B instanceof FeatureVector) {
            return $this->hammingOfFeatureVectors($A, $B);
        }

        throw new \InvalidArgumentException(
            "HammingDistance accepts only strings or FeatureVector instances, not mixed"
        );
    }

    /**
     * Count the number of positions that A and B differ them being strings.
     */
    private function hammingOfStrings($A, $B)
    {
        $l1 = strlen($A);
        $l2 = strlen($B);

        $l = min($l1, $l2);
        $d = 0;
        for ($i=0;$i<$l;$i++) {
            $d += (int) ($A[$i]!=$B[$i]);
        }

        return $d + (int) abs($l1-$l2);
    }

    /**
     * Count the number of dimensions in which the two FeatureVector instances
     * differ.
     */
    private function hammingOfFeatureVectors($A, $B)
    {
        $keys = array();
        foreach ($A as $k=>$v) {
            $keys[$k] = 1;
        }
        foreach ($B as $k=>$v) {
            $keys[$k] = 1;
        }

        $cnt = 0;
        foreach ($keys as $k=>$v) {
            $cnt += (int)($A[$k] !== $B[$k]);
        }

        return $cnt;
    }
}
