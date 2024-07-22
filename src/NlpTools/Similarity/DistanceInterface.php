<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * Distance should return a number proportional to how dissimilar
 * the two instances are(with any metric)
 */
interface DistanceInterface
{
    /**
     * @param  array<int|string, mixed> $a Either feature vector or simply vector
     * @param  array<int|string, mixed> $b Either feature vector or simply vector
     */
    public function dist(array &$a, array &$b): float;
}
