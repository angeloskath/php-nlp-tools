<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;


/**
 * JelinekMercerLM is a class for ranking documents against a query based on Linear interpolation of the maximum 
 * likelihood model.
 *
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The value for λ is generally very small (0.1) for title queries and around 0.7 for verbose.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class JelinekMercerLM implements ScoringInterface
{

    const MU = 0.20;

    protected $math;

    protected $mu;

    public function __construct($mu = self::MU)
    {
        $this->mu = $mu;
        $this->math = new Math();

    }
 
    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount)
    {
        $score = 0;

        if($tf != 0){
            $smoothed_probability = $termFrequency / $collectionLength;
            $score += $this->math->mathLog(1 + (((1 - $this->mu) * $tf) / $docLength) + ($this->mu * $smoothed_probability));
        }

        return $score;

    }

    
}