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

    /**
     * @group Slow
     */
    public function testLargeRandomDataset()
    {
        $index = $this->getSpatialIndexInstance();
        $baseline = new NaiveLinearSearch();
        $index->setDistanceMetric(new Euclidean());
        $baseline->setDistanceMetric(new Euclidean());

        $points = array();
        for ($i=0; $i<10000; $i++) {
            $points[] = array(
                'x'=>mt_rand()/mt_getrandmax(),
                'y'=>mt_rand()/mt_getrandmax()
            );
        }

        $point = $points[array_rand($points, 1)];

        $baseline->index($points);
        $results = $baseline->kNearestNeighbors($point, 5);
        sort($results);

        $index->index($points);
        $results2 = $index->kNearestNeighbors($point, 5);
        sort($results2);

        $this->assertEquals(
            $results,
            $results2
        );
    }
}
