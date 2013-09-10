<?php

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Generators\GeneratorInterface;

/**
 * Implement the gamma distribution.
 * The implementation is ported to php from c++. C++ is written by John
 * D. Cook and can be found at http://www.johndcook.com/SimpleRNG.cpp
 */
class Gamma extends AbstractDistribution
{
    protected $normal;
    protected $gamma;
    protected $shape;
    protected $scale;

    public function __construct($shape,$scale,  GeneratorInterface $rnd=null)
    {
        parent::__construct($rnd);

        $this->scale = $scale;
        $this->shape = abs($shape);
        if ($this->shape >= 1)
            $this->normal = new Normal(0,1,$this->rnd);
        else
            $this->gamma = new Gamma($this->shape + 1, 1, $this->rnd);

    }

    public function sample()
    {
        if ($this->shape >= 1) {
            $d = $this->shape - 1/3;
            $c = 1/sqrt(9*$d);
            for (;;) {
                do {
                    $x = $this->normal->sample();
                    $v = 1 + $c*$x;
                } while ($v <= 0);
                $v = $v*$v*$v;
                $u = $this->rnd->generate();
                $xsq = $x*$x;
                if ($u < 1-.0331*$xsq*$xsq || log($u) < 0.5*$xsq + $d*(1-$v+log($v)))
                    return $this->scale*$d*$v;
            }
        } else {
            $g = $this->gamma->sample();
            $w = $this->rnd->generate();

            return $this->scale*$g*pow($w,1/$this->shape);
        }
    }
}
