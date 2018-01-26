<?php

namespace NlpTools\Ranking\AfterEffect;

use NlpTools\Math\Math;

abstract class AfterEffect
{

    protected $math;


    public function __construct()
    {
        $this->math = new Math();
    }

    abstract protected function gain($tfn, $documentFrequency, $termFrequency);

}