<?php

namespace NlpTools\Similarity;

/**
 * http://en.wikipedia.org/wiki/Sørensen–Dice_coefficient
 */
class TverskyIndex implements SimilarityInterface, DistanceInterface
{
/**
 * The similarity returned by this algorithm is a number between 0,1
 * The algorithm described in http://www.cogsci.ucsd.edu/~coulson/203/tversky-features.pdf, which generalizes both 
 * Dice similarity and Jaccard index, does not meet the criteria for a similarity metric (due to its inherent 
 * assymetry), but has been made symmetrical as applied here (by Jimenez, S., Becerra, C., Gelbukh, A.):
 * http://aclweb.org/anthology/S/S13/S13-1028.pdf
 *
 * An alpha value of 0.5 and beta value of 2 solves Jaccard Index while an alpha value of 0.5 and beta value of 1 solves 
 * Dice Similarity
 *
 * @param  array $A
 * @param  array $B
 * @param  array $alpha (optional)
 * @param  array $beta (optional)
 * @return float
 */

    public function similarity(&$A, &$B, $alpha = 0.5, $beta = 1)
    {

        $a = array_fill_keys($A,1);
        $b = array_fill_keys($B,1);

        $min = min(count(array_diff_key($a,$b)),count(array_diff_key($b, $a)));
        $max = max(count(array_diff_key($a,$b)),count(array_diff_key($b, $a)));

        $intersect = count(array_intersect_key($a,$b));

        return $intersect/($intersect + ($beta * ($alpha * $min + $max*(1-$alpha)) ));
    }


    public function dist(&$A, &$B, $alpha = 0.5, $beta = 1)
    {
        return 1-$this->similarity($A,$B,$alpha,$beta);
    }
}