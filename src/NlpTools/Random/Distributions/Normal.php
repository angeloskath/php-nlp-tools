<?php

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;

class Normal extends AbstractDistribution
{
    protected $m;
    protected $sigma;

    public function __construct($m=0.0,$sigma=1.0, GeneratorInterface $rnd=null)
    {
        parent::__construct($rnd);

        $this->m = $m;
        $this->sigma = abs($sigma);
    }

    public function sample()
    {
        $u1 = $this->rnd->generate();
        $u2 = $this->rnd->generate();
        $r = sqrt(-2*log($u1));
        $theta = 2.0*M_PI*$u2;

        return $this->m + $this->sigma*$r*sin($theta);
    }
}
