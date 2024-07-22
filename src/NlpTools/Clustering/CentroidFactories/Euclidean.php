<?php

declare(strict_types=1);

namespace NlpTools\Clustering\CentroidFactories;

use NlpTools\Clustering\CentroidFactories\CentroidFactoryInterface;

/**
 * Computes the euclidean centroid of the provided sparse vectors
 */
class Euclidean implements CentroidFactoryInterface
{
    /**
     * If the document is a collection of tokens or features transform it to
     * a sparse vector with frequency information.
     *
     * Ex.: If 'A' appears twice in the doc the dimension 'A' will have value 2
     * in the resulting vector
     *
     * @param  array<mixed, mixed> $doc The doc data to transform to sparse vector
     * @return array<mixed, mixed> A sparse vector representing the document to the n-dimensional euclidean space
     */
    protected function getVector(array $doc): array
    {
        if (is_int(key($doc))) {
            return array_count_values($doc);
        }

        return $doc;
    }

    /**
     * Compute the mean value for each dimension.
     *
     * @param  array<int, mixed> $docs   The docs from which the centroid will be computed
     * @param  array<int, int> $choose The indexes from which the centroid will be computed (if empty all the docs will be used)
     * @return array<mixed, mixed> The centroid. It could be any form of data a number, a vector (it will be the same as the data provided in docs)
     */
    public function getCentroid(array &$docs, array $choose = []): array
    {
        $v = [];
        if ($choose === []) {
            $choose = range(0, count($docs) - 1);
        }

        $cnt = count($choose);
        foreach ($choose as $idx) {
            $doc = $this->getVector($docs[$idx]);
            foreach ($doc as $k => $w) {
                if (!isset($v[$k])) {
                    $v[$k] = $w;
                } else {
                    $v[$k] += $w;
                }
            }
        }

        foreach ($v as &$w) {
            $w /= $cnt;
        }

        return $v;
    }
}
