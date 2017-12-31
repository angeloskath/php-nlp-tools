<?php

namespace NlpTools\Similarity;

/**
 * https://en.wikipedia.org/wiki/Overlap_coefficient
 */
class OverlapCoefficient implements SimilarityInterface, DistanceInterface
{
   /**
    * The similarity returned by this algorithm is a number between 0,1
    */
    public function similarity(&$A, &$B)
    {


        $a = array_fill_keys($A,1);
        $b = array_fill_keys($B,1);

        $intersect = count(array_intersect_key($a,$b));
        $a_count = count($a);
        $b_count = count($b);

        return $intersect/min($a_count,$b_count);
    }

    public function dist(&$A, &$B)
    {
        return 1-$this->similarity($A,$B);
    }
}