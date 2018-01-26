<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\ScoringInterface;


/**
 * HiemstraLM is a class for ranking documents against a query based on Hiemstra's PHD thesis for language 
 * model.
 * https://pdfs.semanticscholar.org/67ba/b01706d3aada95e383f1296e5f019b869ae6.pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class HiemstraLM implements ScoringInterface
{

    const C = 0.15;

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
            $score += $this->math->mathlog(1 + ( ($this->c * $tf * $collectionLength) / ((1-$this->c) * $termFrequency * $docLength)));
        }

        return $score;

    }


}