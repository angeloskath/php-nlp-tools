<?php

namespace NlpTools\Ranking;


interface ScoringInterface
{

    public function score($tf, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount);

}
