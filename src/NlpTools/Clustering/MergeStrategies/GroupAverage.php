<?php

declare(strict_types=1);

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\DistanceInterface;

/**
 * In single linkage clustering the new distance of the merged cluster with
 * cluster i is the average distance of all points in cluster x to i and y to i.
 *
 * The average distance is efficiently computed by assuming that every point from
 * every other point in each cluster have the same distance (the average distance).
 * Then the computation is simply a weighted average of the average distances.
 */
class GroupAverage extends HeapLinkage
{
    /**
     * @var array<int, int>
     */
    protected array $clusterSize;

    /**
     * @param array<int, mixed> $docs
     */
    public function initializeStrategy(DistanceInterface $distance, array &$docs): void
    {
        parent::initializeStrategy($distance, $docs);

        $this->clusterSize = array_fill_keys(
            range(0, $this->L - 1),
            1
        );
    }

    protected function newDistance(int $xi, int $yi, int $x, int $y): float
    {
        $size_x = $this->clusterSize[$x];
        $size_y = $this->clusterSize[$y];

        return ($this->dm[$xi] * $size_x + $this->dm[$yi] * $size_y) / ($size_x + $size_y);
    }

    /**
     * @return array<int, mixed>
     */
    public function getNextMerge(): array
    {
        $r = parent::getNextMerge();

        $this->clusterSize[$r[0]] += $this->clusterSize[$r[1]];
        unset($this->clusterSize[$r[1]]);

        return $r;
    }
}
