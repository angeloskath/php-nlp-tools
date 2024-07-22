<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * A Generalization of Jaccard Index and Dice Similarity.
 *
 * The similarity returned by this algorithm is a number between 0,1 The
 * algorithm described in
 * http://www.cogsci.ucsd.edu/~coulson/203/tversky-features.pdf, which
 * generalizes both Dice similarity and Jaccard index, does not meet the
 * criteria for a similarity metric (due to its inherent assymetry), but has
 * been made symmetrical as applied here (by Jimenez, S., Becerra, C., Gelbukh,
 * A.): http://aclweb.org/anthology/S/S13/S13-1028.pdf
 */
class TverskyIndex implements SimilarityInterface, DistanceInterface
{
    /**
     * @param $alpha Set to 0.5 to get either Jaccard Index or Dice Similarity
     * @param $beta  Set to 1 to get Jaccard Index and 2 for Dice Similarity
     */
    public function __construct(public float $alpha = 0.5, public int $beta = 1)
    {
    }

    /**
     * Compute the similarity using the alpha and beta values given in the
     * constructor.
     */
    public function similarity(array &$a, array &$b): float
    {
        $alpha = $this->alpha;
        $beta = $this->beta;

        $aa = array_fill_keys($a, 1);
        $bb = array_fill_keys($b, 1);

        $min = min(count(array_diff_key($aa, $bb)), count(array_diff_key($bb, $aa)));
        $max = max(count(array_diff_key($aa, $bb)), count(array_diff_key($bb, $aa)));

        $intersect = count(array_intersect_key($aa, $bb));

        return $intersect / ($intersect + ($beta * ($alpha * $min + $max * (1 - $alpha)) ));
    }

    public function dist(array &$a, array &$b): float
    {
        return 1 - $this->similarity($a, $b);
    }
}
