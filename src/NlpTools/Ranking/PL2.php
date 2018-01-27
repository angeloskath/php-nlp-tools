<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;


/**
 * PL2 is a DFR class for ranking documents against a query based on Poisson model with Laplace 
 * after-effect and normalisation 2.
 *
 * The implementation is based on G. Amati's paper:
 * http://theses.gla.ac.uk/1570/1/2003amatiphd.pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class PL2 implements ScoringInterface
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
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount)
    {
        $score = 0;

        if($tf != 0){
            $avg_dl = $docLength/$collectionLength;
            $TF = $tf * $this->getTfN2($docLength, $avg_dl);
            $NORM = 1 / ($TF + 1);
            $f = $termFrequency / $collectionCount;
            $score += $NORM  * $keyFrequency 
                      * ($TF * $this->math->DFRlog(1/$f)
                      + $f * $this->math->log2ofE()
                      + 0.5 * $this->math->DFRlog(2 * pi() * $TF)
                      + $TF * ($this->math->DFRlog($TF) - $this->math->log2ofE()));
        }

        return $score;

    }


}