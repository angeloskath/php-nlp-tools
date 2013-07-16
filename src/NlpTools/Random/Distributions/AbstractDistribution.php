<?php

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorI;
use NlpTools\Random\Generators\MersenneTwister;

abstract class AbstractDistribution
{
	protected $rnd;
	
	public function __construct(GeneratorI $rnd=null) {
		if ($rnd == null)
			$this->rnd = MersenneTwister::get();
		else
			$this->rnd = $rnd;
	}
	
	abstract public function sample();
}
