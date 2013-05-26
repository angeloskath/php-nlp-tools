<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\Distance;

/**
 */
class SingleLink extends HeapLinkage
{
	protected function newDistance($xi,$yi,$x,$y) {
		return min($this->heap[$xi],$this->heap[$yi]);
	}
}

