<?php

namespace NlpTools\Similarity;


abstract class DistanceMatrix implements Distance
{
	protected $dist;

	protected $matrix;
	protected $W;

	public function __construct(Distance $d, array $docs=array()) {
		$this->dist = $d;
		if (count($docs)>0)
			$this->initializeMatrix($docs);
	}

	public function initializeMatrix(array $docs) {
		$this->W = count($docs);
		$this->matrix = new \SplFixedArray( $this->W*$this->W );
		for ($x=0;$x<$this->W;$x++) {
			for ($y=$x+1;$y<$this->W;$y++) {
				$d = $this->dist->dist($docs[$x],$docs[$y]);
				$this->matrix[ $y*$this->W + $x ] = $d;
				$this->matrix[ $x*$this->W + $y ] = $d;
				$this->matrix[ $x*$this->W + $x ] = 0;
			}
		}
	}

	public function dist(&$A, &$B) {
		foreach ($A as $a) {
			foreach ($B as $b) {
				$this->feedDocs($a,$b);
			}
		}
		return $this->getComputedDistance();
	}

	abstract protected function feedDocs($a,$b);
	abstract protected function getComputedDistance();

}

