<?php

namespace NlpTools\Similarity;

/**
 * Single link means that the clusters' distance is simply the
 * distance of the two closest documents.
 */
class SingleLink extends DistanceMatrix
{
	protected $tmin = INF;

	protected function feedDocs($a,$b) {
		$d = $this->matrix[ $a*$this->W + $b ];
		if ($this->tmin > $d)
			$this->tmin = $d;
	}

	/**
	 * Return the minimum of the distances between the documents of
	 * the two clusters.
	 */
	protected function getComputedDistance() {
		$t = $this->tmin;
		$this->tmin = INF;
		return $t;
	}
}

