<?php

namespace NlpTools\Similarity;

/**
 * http://en.wikipedia.org/wiki/Jaccard_index
 */
class JaccardIndex implements SimilarityInterface, DistanceInterface
{
    /**
     * The similarity returned by this algorithm is a number between 0,1
     */
    public function similarity(&$A, &$B)
    {
        $a = array_fill_keys($A,1);
        $b = array_fill_keys($B,1);

        $intersect = count(array_intersect_key($a,$b));
        $union = count(array_fill_keys(array_merge($A,$B),1));

        return $intersect/$union;
    }

    /**
     * Jaccard Distance is simply the complement of the jaccard similarity
     */
    public function dist(&$A, &$B)
    {
        return 1-$this->similarity($A,$B);
    }

}
