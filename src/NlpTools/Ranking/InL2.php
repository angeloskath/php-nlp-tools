<?php

namespace App\Utilities\Search;

use App\Utilities\Search\ScoringInterface;
use App\Utilities\Math\Math;


/**
 * InL2 is a DFR class for ranking documents against a query based on Inverse document frequency model
 * for randomness, Laplace succession for first normalisation, and Normalisation 2 for term 
 * frequency normalisation .
 *
 * The implementation is based on G. Amati's paper:
 * http://theses.gla.ac.uk/1570/1/2003amatiphd.pdf
 *
 * Some modifications have been made to allow for non-infinite scoring.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class InL2 implements ScoringInterface
{

    const C = 1.0;

    protected $math;

    protected $c;

    public function __construct($c = self::C)
    {
        $this->c    = 1.0;
        $this->math = new Math();

    }

    /**
     * Returns tf Normalization 2.
     * https://en.wikipedia.org/wiki/Divergence-from-randomness_model#Term_Frequency_Normalization
     *
     * The parameter c can be set automatically, as described by He and Ounis 'Term Frequency Normalisation
     * Tuning for BM25 and DFR model', in Proceedings of ECIR'05, 2005
     * @param  int $length
     * @param  int $avg_dl
     * @return float
     */
    private function getTfN2($docLength, $avg_dl)
    {
        return $this->math->log(1 + ($this->c * $avg_dl)/$docLength);
    }

    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount)
    {
        $score = 0;

        if($tf != 0){
            $avg_dl = $docLength/$collectionLength;
            $TF = $tf * $this->getTfN2($docLength, $avg_dl);
            $NORM = 1 / ($TF + 1);
            $score += ($TF * $this->math->log(($documentFrequency+1)/0.5) * $NORM);
        }

        return $score;

    }

}