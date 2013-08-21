<?php

namespace NlpTools\Clustering\CentroidFactories;

/**
 * MeanAngle computes the unit vector with angle the average of all
 * the given vectors. The purpose is to compute a vector M such that
 * sum(cosine_similarity(M,x_i)) is maximized
 */
class MeanAngle extends Euclidean
{
    protected function normalize(array $v)
    {
        $norm = array_reduce(
            $v,
            function ($v,$w) {
                return $v+$w*$w;
            }
        );
        $norm = sqrt($norm);

        return array_map(
            function ($vi) use ($norm) {
                return $vi/$norm;
            },
            $v
        );
    }

    public function getCentroid(array &$docs, array $choose=array())
    {
        if (empty($choose))
            $choose = range(0,count($docs)-1);
        $cnt = count($choose);
        $v = array();
        foreach ($choose as $idx) {
            $d = $this->normalize($this->getVector($docs[$idx]));
            foreach ($d as $i=>$vi) {
                if (!isset($v[$i]))
                    $v[$i] = $vi;
                else
                    $v[$i] += $vi;
            }
        }

        return array_map(
            function ($vi) use ($cnt) {
                return $vi/$cnt;
            },
            $v
        );
    }
}
