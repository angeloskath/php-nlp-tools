<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\Distance;

abstract class HeapLinkage implements MergeStrategy
{
	protected $L;
	protected $heap;
	protected $dm;
	protected $removed;

	abstract protected function newDistance($xi,$yi,$x,$y);

	public function initializeStrategy(Distance $d, array &$docs) {
		$this->L = count($docs);
		$this->removed = array_fill_keys(range(0, $this->L-1), false);
		$elements = (int)($this->L*($this->L-1))/2;
		$this->dm = new \SplFixedArray($elements);
		$this->heap = new \MatrixHeap($this->dm);
		for ($x=0;$x<$this->L;$x++) {
			for ($y=$x+1;$y<$this->L;$y++) {
				$index = $y*($y-1)/2 + $x;
				$this->dm[$index] = $d->dist($docs[$x],$docs[$y]);
				$this->heap->insert($index);
			}
		}
	}

	public function getNextMerge() {
		$index = $this->heap->extract();
		list($y,$x) = $this->unravelIndex($index);
		while ($this->removed[$y] || $this->removed[$x]) {
			$index = $this->heap->extract();
			list($y,$x) = $this->unravelIndex($index);
		}

		$yi = $this->packIndex($y,0);
		$xi = $this->packIndex($x,0);
		for ($i=0;$i<$x;$i++,$yi++,$xi++)
		{
			$d = $this->newDistance($xi,$yi,$x,$y);
			$this->dm[$xi] = $d;
		}
		for ($i=$x+1;$i<$y;$i++,$yi++)
		{
			$xi = $this->packIndex($i,$x);
			$d = $this->newDistance($xi,$yi,$x,$y);
			$this->dm[$xi] = $d;
		}
		for ($i=$y+1;$i<$this->L;$i++)
		{
			$xi = $this->packIndex($i,$x);
			$yi = $this->packIndex($i,$y);
			$d = $this->newDistance($xi,$yi,$x,$y);
			$this->dm[$xi] = $d;
		}
		
		$this->removed[$y] = true;
		$this->heap->recoverFromCorruption();

		return array($x,$y);
	}

	protected function unravelIndex($index) {
		$a = 0;
		$b = $this->L-1;
		$y = 0;
		while ($b-$a > 1) {
			$y = (int)($a + ($b-$a)/2);
			if ($y*($y+1)/2 > $index) {
				$b = $y;
			} else {
				$a = $y;
			}
		}
		if ($y*($y+1)/2 > $index) {
			$y--;
		}
		return array(
			$y+1,
			$index - ($y*($y+1)/2)
		);
	}
	protected function packIndex($y, $x) {
		return $y*($y-1)/2 + $x;
	}
}

