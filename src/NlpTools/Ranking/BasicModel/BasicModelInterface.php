<?php

namespace NlpTools\Ranking\BasicModel;


interface BasicModelInterface
{

    public function score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount);

}
