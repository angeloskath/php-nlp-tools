<?php

namespace NlpTools\Ranking\BasicModel;

use NlpTools\Ranking\BasicModel\BasicModelInterface;


/**
 * This class implements the BE basic model for randomness. BE stands for Bose-Einstein statistics
 *
 * Gianni Amati and Cornelis Joost Van Rijsbergen. 2002.
 * Probabilistic models of information retrieval based on measuring the
 * divergence from randomness. ACM Trans. Inf. Syst. 20, 4 (October 2002)
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class G extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }
    /**
     * Inf1(tf) = ((-$this->math->log(1/(1+$lambda))) - $tfn) * ($this->math->log($lambda/(1+$lambda)))
     */
    public function score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount){
    	$F = $termFrequency + 1;
    	$N = $collectionCount;
    	$lambda = $termFrequency / ($collectionCount + $termFrequency);

    	// original formula provides negative result, so rewrite to make positive
    	// -log(1 / (lambda + 1)) -> log(lambda + 1)
		return (($this->math->DFRlog($lambda + 1)) - $tfn) * ($this->math->DFRlog((1 + $lambda) / $lambda)) ;

	}

}