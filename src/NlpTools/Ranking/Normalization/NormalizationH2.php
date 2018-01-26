<?php

namespace NlpTools\Ranking\Normalization;

/**
 * The density function of the term frequency is inversely proportional to the length.
 */

class NormalizationH2 extends Normalization implements NormalizationInterface
{

    const C = 1;

	protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c = $c;

    }

    public function normalise($tf, $docLength, $termFrequency, $collectionLength) {
    	$avg_dl = $docLength/$collectionLength;
    	return $tf * $this->math->log(1 + $this->c * $avg_dl / $docLength);
    }

}