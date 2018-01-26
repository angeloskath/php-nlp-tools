<?php

namespace NlpTools\Ranking\BasicModel;

use NlpTools\Math\Math;

abstract class BasicModel
{

    protected $math;


    public function __construct()
    {
        $this->math = new Math();
    }

    abstract protected function score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount);

    protected function idfDFR($collectionCount, $d) {
        return $this->math->log(($collectionCount+1)/($d+0.5));
    }

}