<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;


/**
 * AbsoluteDiscountingLM is a class for ranking documents against a query by lowering down the probability of seen words by
 * subtracting a constant from their counts.
 *
 * The effect of this is that the events with the lowest counts are discounted relatively more than those with higher counts.
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The optimal value for 𝛿 tends to be around 0.7
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class AbsoluteDiscountingLM implements ScoringInterface
{

    const DELTA = 0.7;

    protected $math;

    protected $delta;

    public function __construct($delta = self::DELTA)
    {
        $this->delta = $delta;
        $this->math = new Math();

    }
 
 
    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount)
    {
        $score = 0;
        
        if($tf != 0){
            $smoothed_probability = $termFrequency / $collectionLength;
            $sigma = ($this->delta * $uniqueTermsCount) / $docLength;
            $score += $this->math->mathLog(1 + ((max($tf - $this->delta, 0) / $docLength) + ($sigma * $smoothed_probability)));
        }

        return $score;

    }

    
}