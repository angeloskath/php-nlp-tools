<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * https://en.wikipedia.org/wiki/Overlap_coefficient
 */
class OverlapCoefficient implements SimilarityInterface, DistanceInterface
{
   /**
    * The similarity returned by this algorithm is a number between 0,1
    */
    public function similarity(array &$a, array &$b): float
    {
        // Make the arrays into sets
        $aa = array_fill_keys($a, 1);
        $bb = array_fill_keys($b, 1);

        // Count the cardinalities of the sets
        $aCount = count($aa);
        $bCount = count($bb);

        if ($aCount === 0 || $bCount === 0) {
            return 0;
        }

        // Compute the intersection and count its cardinality
        $intersect = count(array_intersect_key($aa, $bb));

        return $intersect / min($aCount, $bCount);
    }

    public function dist(array &$a, array &$b): float
    {
        return 1 - $this->similarity($a, $b);
    }
}
