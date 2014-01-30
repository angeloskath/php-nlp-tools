<?php

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
    protected $cluster_size;

    public function initializeStrategy(DistanceInterface $d, array &$docs)
    {
        parent::initializeStrategy($d,$docs);

        $this->cluster_size = array_fill_keys(
            range(0,$this->L-1),
            1
        );
    }

    protected function newDistance($xi,$yi,$x,$y)
    {
        $size_x = $this->cluster_size[$x];
        $size_y = $this->cluster_size[$y];

        return ($this->dm[$xi]*$size_x + $this->dm[$yi]*$size_y)/($size_x + $size_y);
    }

    public function getNextMerge()
    {
        $r = parent::getNextMerge();

        $this->cluster_size[$r[0]] += $this->cluster_size[$r[1]];
        unset($this->cluster_size[$r[1]]);

        return $r;
    }
}
