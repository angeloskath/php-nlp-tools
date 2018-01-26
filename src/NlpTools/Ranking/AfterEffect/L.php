<?php

namespace NlpTools\Ranking\AfterEffect;


class L extends AfterEffect implements AfterEffectInterface
{

    protected $math;


    public function __construct()
    {
        parent::__construct();
    }

    public function gain($tfn, $documentFrequency, $termFrequency) {
    	return 1/(1+$tfn);
    }

}