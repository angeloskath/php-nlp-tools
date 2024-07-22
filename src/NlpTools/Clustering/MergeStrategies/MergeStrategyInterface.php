<?php

declare(strict_types=1);

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\DistanceInterface;

/**
 * In hierarchical agglomerative clustering each document starts in its own
 * cluster and then it is subsequently merged with the "closest" cluster.
 * The MergeStrategy defines how a new distance for the merged cluster is
 * going to be calculated based on the distances of the individual clusters.
 */
interface MergeStrategyInterface
{
    /**
     * Study the docs and preprocess anything required for
     * computing the merges
     *
     * @param array<int, mixed> $docs
     */
    public function initializeStrategy(DistanceInterface $distance, array &$docs): void;

    /**
     * Return the next two clusters for merging and assume
     * they are merged (ex. update a similarity matrix)
     *
     * @return array<int, mixed> An array with two numbers which are the cluster ids
     */
    public function getNextMerge(): array;
}
