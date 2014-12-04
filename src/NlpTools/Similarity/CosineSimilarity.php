<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\FeatureVector;

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
     * @param  FeatureVector $A A feature vector
     * @param  FeatureVector $B Another feature vector
     * @return float         The cosinus of the angle between the two vectors
     */
    public function similarity($A, $B)
    {
        if (!($A instanceof FeatureVector) || !($B instanceof FeatureVector)) {
            throw new \InvalidArgumentException(
                "CosineSimilarity accepts only FeatureVector instances"
            );
        }

        $prod = 0.0;
        $A_norm = 0.0;
        foreach ($A as $i=>$xi) {
            if (isset($B[$i])) {
                $prod += $xi*$B[$i];
            }
            $A_norm += $xi*$xi;
        }
        $A_norm = sqrt($A_norm);
        if ($A_norm==0) {
            throw new \InvalidArgumentException(
                "Vector \$A is the zero vector"
            );
        }

        $B_norm = 0.0;
        foreach ($B as $i=>$xi) {
            $B_norm += $xi*$xi;
        }
        $B_norm = sqrt($B_norm);
        if ($B_norm==0) {
            throw new \InvalidArgumentException(
                "Vector \$B is the zero vector"
            );
        }

        return $prod/($A_norm*$B_norm);
    }

    /**
     * Cosine distance is simply 1-cosine similarity
     */
    public function dist($A, $B)
    {
        return 1-$this->similarity($A, $B);
    }
}
