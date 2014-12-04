<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\FeatureVector;

/**
 * http://en.wikipedia.org/wiki/Jaccard_index
 */
class JaccardIndex implements SimilarityInterface, DistanceInterface
{
    /**
     * The similarity returned by this algorithm is a number between 0,1
     */
    public function similarity($A, $B)
    {
        if (!($A instanceof FeatureVector) || !($B instanceof FeatureVector)) {
            throw new \InvalidArgumentException(
                "JaccardIndex accepts only FeatureVector instances"
            );
        }

        $a = array();
        $b = array();
        foreach ($A as $k=>$v) {
            $a[$k] = 1;
        }
        foreach ($B as $k=>$v) {
            $b[$k] = 1;
        }

        $intersect = count(array_intersect_key($a, $b));
        $union = count($a+$b);

        return $intersect/$union;
    }

    /**
     * Jaccard Distance is simply the complement of the jaccard similarity
     */
    public function dist($A, $B)
    {
        return 1-$this->similarity($A, $B);
    }
}
