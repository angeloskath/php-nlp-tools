<?php

namespace NlpTools\Similarity;

/**
 * Given two vectors compute cos(theta) where theta is the angle
 * between the two vectors in a N-dimensional vector space.
 * 
 * cos(theta) = A•B / |A||B|
 * '•' means inner product
 * 
 * Since the vectors are meant to be feature vectors, the value of
 * each vector for each dimension is simply the frequency of this
 * feature. Moreover, there cannot be negative frequency of occurence so
 * there cannot be negative vector coefficients and the angle will
 * always be between 0 and pi/2.
 */
class CosineSimilarity implements Similarity, Distance
{
	
	/**
	 * Returns a number between 0,1 that corresponds to the cos(theta)
	 * where theta is the angle between the two sets if they are treated
	 * as n-dimensional vectors.
	 * 
	 * See the class comment about why the number is in [0,1] and not
	 * in [-1,1] as it normally should.
	 */
	public function similarity(&$A, &$B) {
		// This means they are simple text vectors
		// so we need to count to make them vectors
		if (key($A)===0)
			$v1 = array_count_values($A);
		else
			$v1 = &$A;
		if (key($B)===0)
			$v2 = array_count_values($B);
		else
			$v2 = &$B;
		
		$prod = 0.0;
		$v1_norm = 0.0;
		foreach ($v1 as $i=>$xi) {
			if (isset($v2[$i])) {
				$prod += $xi*$v2[$i];
			}
			$v1_norm += $xi*$xi;
		}
		$v1_norm = sqrt($v1_norm);
		
		$v2_norm = 0.0;
		foreach ($v2 as $i=>$xi) {
			$v2_norm += $xi*$xi;
		}
		$v2_norm = sqrt($v2_norm);
		
		return $prod/($v1_norm*$v2_norm);
	}
	
	/**
	 * Cosine distance is simply 1-cosine similarity
	 */
	public function dist(&$A, &$B) {
		return 1-$this->similarity($A,$B);
	}
}

?>
