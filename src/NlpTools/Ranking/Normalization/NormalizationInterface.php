<?php

namespace NlpTools\Ranking\Normalization;


interface NormalizationInterface
{

    public function normalise($tf, $docLength, $termFrequency, $collectionLength);

}
