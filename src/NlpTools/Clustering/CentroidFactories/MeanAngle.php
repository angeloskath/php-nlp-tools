<?php

declare(strict_types=1);

namespace NlpTools\Clustering\CentroidFactories;

/**
 * MeanAngle computes the unit vector with angle the average of all
 * the given vectors. The purpose is to compute a vector M such that
 * sum(cosine_similarity(M,x_i)) is maximized
 */
class MeanAngle extends Euclidean
{
    /**
     * @param array<int, mixed> $v
     * @return array<int, mixed>
     */
    protected function normalize(array $v): array
    {
        $norm = array_reduce(
            $v,
            fn($v, $w): float|int => $v + $w * $w
        );
        $norm = sqrt($norm);

        return array_map(
            fn($vi): float => $vi / $norm,
            $v
        );
    }

    /**
     * @param array<int, mixed> $docs
     * @param array<int, int> $choose
     * @return array<mixed, mixed>
     */
    public function getCentroid(array &$docs, array $choose = []): array
    {
        if ($choose === []) {
            $choose = range(0, count($docs) - 1);
        }

        $cnt = count($choose);
        $v = [];
        foreach ($choose as $idx) {
            $d = $this->normalize($this->getVector($docs[$idx]));
            foreach ($d as $i => $vi) {
                if (!isset($v[$i])) {
                    $v[$i] = $vi;
                } else {
                    $v[$i] += $vi;
                }
            }
        }

        return array_map(
            fn($vi): int|float => $vi / $cnt,
            $v
        );
    }
}
