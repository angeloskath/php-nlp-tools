<?php

declare(strict_types=1);

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;

class Normal extends AbstractDistribution
{
    public function __construct(protected float $m = 0.0, protected float $sigma = 1.0, GeneratorInterface $generator = null)
    {
        parent::__construct($generator);
        $this->sigma = abs($sigma);
    }

    public function sample(): float
    {
        $u1 = $this->rnd->generate();
        $u2 = $this->rnd->generate();
        $r = sqrt(-2 * log($u1));
        $theta = 2.0 * M_PI * $u2;

        return $this->m + $this->sigma * $r * sin($theta);
    }
}
