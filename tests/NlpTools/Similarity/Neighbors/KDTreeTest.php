<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\Euclidean;

class KDTreeTest extends NeighborsTestAbstract
{
    protected function getSpatialIndexInstance()
    {
        return new KDTree();
    }

    public function testDatasetWithDuplicates()
    {
        $index = $this->getSpatialIndexInstance();
        $index->setDistanceMetric(new Euclidean());
        $points = array(
            array('x'=>1, 'y'=>2),
            array('x'=>1, 'y'=>2),
            array('x'=>1, 'y'=>2),
            array('x'=>1, 'y'=>2),
            array('x'=>1, 'y'=>2),
            array('x'=>1, 'y'=>2),
            array('x'=>2, 'y'=>2)
        );
        $index->index($points);
        $point = array('x'=>1, 'y'=>2);

        $idxs = $index->kNearestNeighbors($point, 6);
        sort($idxs);
        $this->assertEquals(
            array(0,1,2,3,4,5),
            $idxs
        );
    }
}
