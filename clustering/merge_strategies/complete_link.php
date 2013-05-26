<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\Distance;

/**
 */
class CompleteLink extends HeapLinkage
{
	protected function newDistance($xi,$yi,$x,$y) {
		return max($this->heap[$xi],$this->heap[$yi]);
	}
}

