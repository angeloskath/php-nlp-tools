<?php

namespace NlpTools\Ranking\Normalization;

use NlpTools\Math\Math;

abstract class Normalization
{

    protected $math;


    public function __construct()
    {
        $this->math = new Math();
    }

    abstract protected function normalise($tf, $docLength, $termFrequency, $collectionLength);

}