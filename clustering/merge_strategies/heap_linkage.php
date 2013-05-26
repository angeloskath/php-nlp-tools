<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\Distance;

abstract class HeapLinkage implements MergeStrategy
{
	protected $L;
	protected $heap;

	abstract protected function newDistance($xi,$yi,$x,$y);

	protected function getHeap() {
		return new \AssociativeHeap\MinHeap();
	}

	public function initializeStrategy(Distance $d, array &$docs) {
		$this->heap = $this->getHeap();

		$this->L = count($docs);
		for ($x=0;$x<$this->L;$x++)
			for ($y=$x+1;$y<$this->L;$y++)
				$this->heap[$y*$this->L + $x] = $d->dist($docs[$x],$docs[$y]);
	}

	protected function updateHeap($xi,$yi,$x,$y) {
		if (isset($this->heap[$xi]))
		{
			if (!isset($this->heap[$yi]))
				continue;
			$d = $this->newDistance($xi,$yi,$x,$y);
			$this->heap[$xi] = $d;
			unset($this->heap[$yi]);
		}
		else if (isset($this->heap[$yi]))
		{
			$this->heap[$xi] = $this->heap[$yi];
			unset($this->heap[$yi]);
		}
	}

	public function getNextMerge() {
		list($key,$d) = $this->heap->extract();
		$y = (int)($key/$this->L);
		$x = $key % $this->L;

		for ($i=0;$i<$x;$i++)
		{
			$xi = $x*$this->L + $i;
			$yi = $y*$this->L + $i;
			$this->updateHeap($xi,$yi,$x,$y);
		}
		for ($i=$x+1;$i<$y;$i++)
		{
			$xi = $i*$this->L + $x;
			$yi = $y*$this->L + $i;
			$this->updateHeap($xi,$yi,$x,$y);
		}
		for ($i=$y+1;$i<$this->L;$i++)
		{
			$xi = $i*$this->L + $x;
			$yi = $i*$this->L + $y;
			$this->updateHeap($xi,$yi,$x,$y);
		}
		return array($x,$y);
	}
}

