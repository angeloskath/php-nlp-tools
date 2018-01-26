<?php

namespace NlpTools\Ranking\Normalization;

/**
 * Assuming we can represent the term frequency within a document
 * as a density function, we can take this to be a uniform distribution; that is,
 * the density function of the term frequency is constant. The H1 hypothesis is a
 * variant of the verbosity principle of Robertson [Robertson and Walker 1994]
 */

class NormalizationH1 extends Normalization implements NormalizationInterface
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
    	return $tf * $this->c * ($avg_dl / $docLength);
    }

}