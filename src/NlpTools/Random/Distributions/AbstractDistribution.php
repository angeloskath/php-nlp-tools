<?php

declare(strict_types=1);

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;
use NlpTools\Random\Generators\MersenneTwister;

abstract class AbstractDistribution
{
    protected GeneratorInterface $rnd;

    public function __construct(?GeneratorInterface $generator = null)
    {
        $this->rnd = $generator ?? MersenneTwister::get();
    }

    abstract public function sample(): mixed;
}
