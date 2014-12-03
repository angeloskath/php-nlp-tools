<?php

namespace NlpTools\Clustering\CentroidFactories;

use NlpTools\FeatureVector\ArrayFeatureVector;
use NlpTools\FeatureVector\FeatureVector;

/**
 * MeanAngle computes the unit vector with angle the average of all
 * the given vectors. The purpose is to compute a vector M such that
 * sum(cosine_similarity(M,x_i)) is maximized
 */
class MeanAngle extends CentroidFactoryInterface
{
    protected function normalize(FeatureVector $vector)
    {
        $norm = 0;
        foreach ($vector as $k=>$v)
            $norm += $v*$v;
        $norm = sqrt($norm);

        $normalized = array();
        foreach ($vector as $k=>$v)
            $normalized[$k] = $v/$norm;

        return $normalized;
    }

    public function getCentroid(array &$docs, array $choose=array())
    {
        if (empty($choose))
            $choose = range(0,count($docs)-1);
        $cnt = count($choose);
        $v = array();
        foreach ($choose as $idx) {
            $d = $this->normalize($docs[$idx]);
            foreach ($d as $i=>$vi) {
                if (!isset($v[$i]))
                    $v[$i] = $vi;
                else
                    $v[$i] += $vi;
            }
        }

        return new ArrayFeatureVector(array_map(
            function ($vi) use ($cnt) {
                return $vi/$cnt;
            },
            $v
        ));
    }
}
