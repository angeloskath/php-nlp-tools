<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * http://en.wikipedia.org/wiki/Sørensen–Dice_coefficient
 */
class DiceSimilarity implements SimilarityInterface, DistanceInterface
{
    /**
     * The similarity returned by this algorithm is a number between 0,1
     *
     * @param  array<int|string, mixed> $a Either feature vector or simply vector
     * @param  array<int|string, mixed> $b Either feature vector or simply vector
     */
    public function similarity(array &$a, array &$b): float
    {
        $aa = array_fill_keys($a, 1);
        $bb = array_fill_keys($b, 1);

        $intersect = count(array_intersect_key($aa, $bb));
        $aCount = count($aa);
        $bCount = count($bb);

        return (2 * $intersect) / ($aCount + $bCount);
    }

    /**
     * @param  array<int|string, mixed> $a Either feature vector or simply vector
     * @param  array<int|string, mixed> $b Either feature vector or simply vector
     */
    public function dist(array &$a, array &$b): float
    {
        return 1 - $this->similarity($a, $b);
    }
}
