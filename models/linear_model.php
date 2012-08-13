<?php

namespace NlpTools;

/*
 * This class represents a linear model of the following form
 * f(x_vec) = l1*x1 + l2*x2 + l3*x3 ...
 */
class LinearModel
{
	protected $l;
	public function __construct(array $l) {
		$this->l = $l;
	}
	public function getWeight($feature) {
		if (!isset($this->l[$feature])) return 0;
		else return $this->l[$feature];
	}
	
	public function getWeights() {
		return $this->l;
	}
}

?>
