<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\Distance;

class NaiveLinearSearch implements SpatialIndexInterface
{
    protected $dist;
    protected $docs_copy;

    public function setDistanceMetric(Distance $d)
    {
        $this->dist = $d;
    }

    public function index(array &$docs)
    {
        $this->docs_copy = &$docs;
    }

    public function add($doc)
    {
        $this->docs_copy[] = $doc;
    }

    public function regionQuery($doc, $e)
    {
        $idxs = array();
        foreach ($this->docs_copy as $idx=>$d) {
            if ($this->dist->dist($d,$doc) < $e) {
                $idxs[] = $idx;
            }
        }
        return $idxs;
    }

    public function kNearestNeighbors($doc, $k)
    {
        $neighbors = array_fill_keys(
            range(0,$k-1),
            array(-1,INF)
        );
        $last = $k-1;
        foreach ($this->docs_copy as $idx=>$d) {
            $dist = $this->dist->dist($doc, $d);
            if ($dist < $neighbors[$last][1]) {
                $neighbors[$last][1] = $dist;
                $neighbors[$last][0] = $idx;
                for ($j=$last-1;$j>0;$j++) {
                    if ($neighbors[$j][1] < $neighbors[$j+1][1]) {
                        break;
                    }
                    $tmp = $neighbors[$j];
                    $neighbors[$j] = $neighbors[$j+1];
                    $neighbors[$j+1] = $tmp;
                }
            }
        }
        return array_map(
            function ($n) {
                return $n[0];
            },
            $neighbors
        );
    }
}
