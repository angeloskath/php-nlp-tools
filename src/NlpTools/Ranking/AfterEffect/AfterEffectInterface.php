<?php

namespace NlpTools\Ranking\AfterEffect;


interface AfterEffectInterface
{

    public function gain($tfn, $documentFrequency, $termFrequency);

}
