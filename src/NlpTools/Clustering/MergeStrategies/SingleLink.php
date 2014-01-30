<?php

namespace NlpTools\Clustering\MergeStrategies;

/**
 * In single linkage clustering the new distance of the merged cluster with
 * cluster i is the smallest distance of either cluster x to i or y to i.
 *
 * Example:
 *
 * Suppose we have the following four clusters
 *
 * a = [ (0,0) ]
 * b = [ (5,2) ]
 * c = [ (0,5) ]
 * d = [ (0,2) ]
 *
 * with the following pairwise distance matrix
 *
 *       a     b     c     d
 *   +-----+-----+-----+-----+
 * a |   0 | 5.3 |   5 |   2 |
 *   +-----+-----+-----+-----+
 * b | 5.3 |   0 | 5.8 |   5 |
 *   +-----+-----+-----+-----+
 * c |   5 | 5.8 |   0 |   3 |
 *   +-----+-----+-----+-----+
 * d |   2 |   5 |   3 |   0 |
 *   +-----+-----+-----+-----+
 *
 * if we merge clusters a,d (which are the closest) then we need to update the
 * matrix to represent the new distances. For every other cluster (b and c) the
 * new distance has to be calculated and it is going to be the minimum between
 * the distances of the two clusters to be merged.
 *
 *               a,d             b             c
 *     +-------------+-------------+-------------+
 * a,d |           0 | min(5.3, 2) |   min(5, 3) |
 *     +-------------+-------------+-------------+
 *   b | min(5.3, 2) |           0 |         5.8 |
 *     +-------------+-------------+-------------+
 *   c |   min(5, 3) |         5.8 |           0 |
 *     +-------------+-------------+-------------+
 *
 */
class SingleLink extends HeapLinkage
{
    protected function newDistance($xi,$yi,$x,$y)
    {
        return min($this->dm[$xi],$this->dm[$yi]);
    }
}
