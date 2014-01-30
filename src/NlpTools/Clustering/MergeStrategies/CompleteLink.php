<?php

namespace NlpTools\Clustering\MergeStrategies;

/**
 * In single linkage clustering the new distance of the merged cluster with
 * cluster i is the maximum distance of either cluster x to i or y to i.
 *
 * For a more detailed description see the documentation of SingleLink.
 */
class CompleteLink extends HeapLinkage
{
    protected function newDistance($xi,$yi,$x,$y)
    {
        return max($this->dm[$xi],$this->dm[$yi]);
    }
}
