<?php

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;

/**
 * Implement a k-dimensional Dirichlet distribution using draws from
 * k gamma distributions and then normalizing.
 */
class Dirichlet extends AbstractDistribution
{
    protected $gamma;

    public function __construct($a,$k,GeneratorInterface $rnd=null)
    {
        parent::__construct($rnd);

        $k = (int) abs($k);
        if (!is_array($a)) {
            $a = array_fill_keys(range(0,$k-1),$a);
        }

        $rnd = $this->rnd;
        $this->gamma = array_map(
            function ($a) use ($rnd) {
                return new Gamma($a,1,$rnd);
            },
            $a
        );
    }

    public function sample()
    {
        $y = array();
        foreach ($this->gamma as $g) {
            $y[] = $g->sample();
        }
        $sum = array_sum($y);

        return array_map(
            function ($y) use ($sum) {
                return $y/$sum;
            },
            $y
        );
    }
}
