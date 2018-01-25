<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;


/**
 * Divergence from Independence (DFI) is a class for ranking documents against a query based on 
 * Chi-square statistics.
 * Kocabas, Dincer & Karaoglan
 * http://dx.doi.org/10.1007/s10791-013-9225-4
 * http://trec.nist.gov/pubs/trec18/papers/muglau.WEB.MQ.pdf
 * It is recommended NOT to remove "stopwords list". From their intro:
 *
 * -- Their observed frequencies in individual documents is expected to fluctuate around their frequencies
 * expected under independence, such words can be modeled as if they were tags. --
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DFI implements ScoringInterface
{

    const SATURATED = 1;

    const CHI_SQUARED = 2;

    const STANDARDIZED = 3;

    protected $math;

    protected $type;

    public function __construct($type = self::CHI_SQUARED)
    {
        $this->type = $type;
        $this->math = new Math();

    }
 
    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount)
    {
        $score = 0;
        $expected = ($termFrequency * $docLength) / $collectionLength;

        if($tf <= $expected){
            return $score;
        }

            if($this->type == self::SATURATED) {
                $measure = ($tf - $expected)/$expected;
            } elseif($this->type == self::STANDARDIZED) {
                $measure = ($tf - $expected) / sqrt($expected);
            } elseif($this->type == self::CHI_SQUARED) {
                $measure = $this->math->pow(($tf - $expected), 2)/$expected;
            }
            $score += $this->math->log($measure + 1);
            return $score;
        

    }

    
}