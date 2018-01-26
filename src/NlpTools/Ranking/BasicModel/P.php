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


class P extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    /**
     * Inf1(tf ) = −tf · log2 λ + λ · log2 e + log2(tf !)
     */ 
    public function score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount){

        $f = (1 * $termFrequency) / (1 * $collectionCount);
		return ($tfn * $this->math->log(1 / $f)
                + $f * $this->math->log2ofE()
                + 0.5 * $this->math->log(2 * pi() * $tfn)
                + $tfn * ($this->math->log($tfn) - $this->math->log2ofE()));

	}

}