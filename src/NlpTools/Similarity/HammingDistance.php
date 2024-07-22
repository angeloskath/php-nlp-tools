<?php

declare(strict_types=1);

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
     * @param  array<int|string, mixed> $a Either a vector or a collection of tokens to be transformed to a vector
     * @param  array<int|string, mixed> $b Either a vector or a collection of tokens to be transformed to a vector
     */
    public function dist(array &$a, array &$b): float
    {
        $aa = $a[0];
        $bb = $b[0];

        $l1 = strlen((string) $aa);
        $l2 = strlen((string) $bb);
        $l = min($l1, $l2);
        $d = 0;
        for ($i = 0; $i < $l; $i++) {
            $d += (int) ($aa[$i] !== $bb[$i]);
        }

        return $d + (int) abs($l1 - $l2);
    }
}
