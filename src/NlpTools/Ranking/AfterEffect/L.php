<?php

namespace NlpTools\Ranking\AfterEffect;


class L extends AfterEffect implements AfterEffectInterface
{

    public function gain($tfn, $documentFrequency, $termFrequency) {
    	return 1/(1+$tfn);
    }

}