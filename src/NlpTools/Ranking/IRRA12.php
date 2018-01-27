<?php

namespace NlpTools\Ranking;

use NlpTools\Ranking\ScoringInterface;


/**
 * An experimental IRRA system that aims to evaluate a new DFI-based term weighting model developed on the basis of
 * Shannon’s information theory (Shannon, 1949), along with the evaluation of a heuristic approach that
 * is expected to provide early precision when used together with DFI term weighting.
 * http://trec.nist.gov/pubs/trec21/papers/irra.web.nb.pdf
 * 
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IRRA12 implements ScoringInterface
{

    /**
     * ∑qtf × ∆(Iij) × Λij
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount)
    {
        $score = 0;

        // eij+
        $expected = (($termFrequency +1 ) * ($docLength + 1)) / ($collectionLength + 1);

        if($tf <= $expected){
            return $score;
        }
            $alpha = ($docLength - $tf) / $docLength;
            $beta = (2/3) * (($tf + 1)/$tf);
            // Λij
            $suppress_junk = pow($alpha, (3/4)) * pow($beta, (1/4));
            // ∆(Iij)
            $score += (($tf + 1) * log(($tf + 1)/sqrt($expected))) - ($tf * log($tf/sqrt($expected)));
            return $score * $keyFrequency * $suppress_junk;
        

    }

    
}