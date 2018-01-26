<?php

namespace NlpTools\Ranking\AfterEffect;

abstract class AfterEffect
{

    abstract protected function gain($tfn, $documentFrequency, $termFrequency);

}