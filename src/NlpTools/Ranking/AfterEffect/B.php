<?php

namespace NlpTools\Ranking\AfterEffect;


class B extends AfterEffect implements AfterEffectInterface
{


    public function gain($tfn, $documentFrequency, $termFrequency) {
    	return ($termFrequency + 1) / ($documentFrequency * ($tfn + 1));
    }

}