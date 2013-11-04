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
 *
 * If the current key of the passed array is not the number 0 then the feature
 * vector is supposed to have been passed as a mapping between the feature name
 * and a value like the following
 * array(
 * 	'feature_1'=>1,
 * 	'feature_2'=>0.55,
 * 	'feature_3'=>12.7,
 * 	....
 * )
 */
class CosineSimilarity implements SimilarityInterface, DistanceInterface
{

    /**
     * Returns a number between 0,1 that corresponds to the cos(theta)
     * where theta is the angle between the two sets if they are treated
     * as n-dimensional vectors.
     *
     * See the class comment about why the number is in [0,1] and not
     * in [-1,1] as it normally should.
     *
     * TODO: Assert $A, $B are arrays
     *
     * @param  array $A Either feature vector or simply vector
     * @param  array $B Either feature vector or simply vector
     * @return float The cosinus of the angle between the two vectors
     */
    public function similarity(&$A, &$B)
    {
        // This means they are simple text vectors
        // so we need to count to make them vectors
        if (is_int(key($A)))
            $v1 = array_count_values($A);
        else
            $v1 = &$A;
        if (is_int(key($B)))
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
        if ($v1_norm==0)
            throw new \InvalidArgumentException("Vector \$A is the zero vector");

        $v2_norm = 0.0;
        foreach ($v2 as $i=>$xi) {
            $v2_norm += $xi*$xi;
        }
        $v2_norm = sqrt($v2_norm);
        if ($v2_norm==0)
            throw new \InvalidArgumentException("Vector \$B is the zero vector");

        return $prod/($v1_norm*$v2_norm);
    }

    /**
     * Cosine distance is simply 1-cosine similarity
     */
    public function dist(&$A, &$B)
    {
        return 1-$this->similarity($A,$B);
    }
}
