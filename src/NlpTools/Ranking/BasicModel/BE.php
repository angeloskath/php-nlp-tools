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


class BE extends BasicModel implements BasicModelInterface
{
	public function __construct()
    {
        parent::__construct();

    }

    /** Inf1(tf) = − log2(N − 1) − log2(e) + f (N + F − 1, N + F − tf − 2) − f (F, F − tf )
     * where f(n, m) is a stirling formula
     */
    public function score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount){
		return (
				- $this->math->log($collectionCount - 1)
				- $this->math->log2ofE()
				+ $this->math->stirlingPower(
					$collectionCount
						+ $termFrequency
						- 1,
					$collectionCount
						+ $termFrequency
						- $tfn
						- 2)
				- $this->math->stirlingPower($termFrequency, $termFrequency - $tfn)
			);
	}

}