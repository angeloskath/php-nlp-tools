<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;


/**
 * DirichletLM is a class for ranking documents against a query based on Bayesian smoothing with 
 * Dirichlet Prior for language modelling.
 *
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DirichletLM implements ScoringInterface
{

    const C = 2500;

    protected $math;

    protected $c;

    public function __construct($c = self::C)
    {
        $this->c    = $c;
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
            $score += $this->math->log(1 + ($tf/($this->c * ($termFrequency / $collectionLength))) ) + $this->math->log($this->c/($docLength+$this->c));
        }

        return $score;

    }

    
}