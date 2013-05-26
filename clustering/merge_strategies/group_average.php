<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\Distance;

/**
 */
class GroupAverage extends HeapLinkage
{
	protected $cluster_size;

	public function initializeStrategy(Distance $d, array &$docs) {
		parent::initializeStrategy($d,$docs);

		$this->cluster_size = array_fill_keys(
			range(0,$this->L-1),
			1
		);
	}

	protected function newDistance($xi,$yi,$x,$y) {
		return $this->heap[$xi]*$this->cluster_size[$x] + $this->heap[$yi]*$this->cluster_size[$y];
	}

	public function getNextMerge() {
		$r = parent::getNextMerge();

		$this->cluster_size[$r[0]] += $this->cluster_size[$r[1]];
		unset($this->cluster_size[$r[1]]);
		return $r;
	}
}

