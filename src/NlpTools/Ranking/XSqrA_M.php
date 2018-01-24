<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;

/**
 * XSqrA_M is a class that implements the XSqrA_M weighting model which computed the 
 * inner product of Pearson's X^2 with the information growth computed 
 * with the multinomial M.
 *
 * Frequentist and Bayesian approach to  Information Retrieval. G. Amati. In 
 * Proceedings of the 28th European Conference on IR Research (ECIR 2006). 
 * LNCS vol 3936, pages 13--24.
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class XSqrA_M implements ScoringInterface
{

    protected $math;
    
    public function __construct()
    {
        $this->math = new Math();
    }

    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount)
    {
        $score = 0;

        if($tf != 0){
            $mle = $tf/$docLength;

            $smoothedProbability = ($tf + 1)/($docLength + 1);

            $collectionPrior = $termFrequency/$collectionLength;

            $XSqrA = $this->math->pow(1-$mle,2)/($tf+1);  

            $InformationDelta =  (($tf+1) * $this->math->log($smoothedProbability/$collectionPrior) - $tf*$this->math->log($mle /$collectionPrior) +0.5*$this->math->log($smoothedProbability/$mle));

            $score += $keyFrequency * $tf * $XSqrA * $InformationDelta;
        }

        return $score;

    }

}