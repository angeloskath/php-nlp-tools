<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\DistanceInterface;
use NlpTools\Similarity\Euclidean;

abstract class NeighborsTestAbstract extends \PHPUnit_Framework_TestCase
{
    abstract protected function getSpatialIndexInstance();

    protected function getPoints()
    {
        return array(
                array('x'=>0,'y'=>0), // 0
                array('x'=>1,'y'=>1), // 1
                array('x'=>2,'y'=>2), // 2
                array('x'=>3,'y'=>3), // 3
                array('x'=>4,'y'=>2), // 4
                array('x'=>5,'y'=>1), // 5
                array('x'=>6,'y'=>0), // 6
                array('x'=>5,'y'=>0), // 7
                array('x'=>4,'y'=>0), // 8
                array('x'=>3,'y'=>0), // 9
                array('x'=>2,'y'=>0), // 10
                array('x'=>1,'y'=>0), // 11
                array('x'=>2,'y'=>1), // 12
                array('x'=>3,'y'=>2), // 13
                array('x'=>4,'y'=>1)  // 14
        );
    }

    public function provideRegionQueries()
    {
        $dist = new Euclidean();
        $points = $this->getPoints();
        return array(
            array($dist, $points, array('x'=>3,'y'=>3), 1.1, array(3,13))
        );
    }

    public function provideKNNQueries()
    {
        $dist = new Euclidean();
        $points = $this->getPoints();
        return array(
            array($dist, $points, array('x'=>3, 'y'=>3), 1, array(3)),
            array($dist, $points, array('x'=>3, 'y'=>3), 2, array(3,13))
        );
    }

    /**
     * The nearest neighbor of a point already in the set is itsself
     *
     * @dataProvider provideKNNQueries
     */
    public function testKNN(DistanceInterface $dist, $points, $point, $k, $results)
    {
        $index = $this->getSpatialIndexInstance();
        $index->setDistanceMetric($dist);
        $index->index($points);

        $idxs = $index->kNearestNeighbors($point, $k);

        $this->assertEquals(
            $results,
            $idxs
        );
    }

    /**
     * @dataProvider provideRegionQueries
     */
    public function testRegionQueries(DistanceInterface $dist, $points, $point, $eps, $results)
    {
        $index = $this->getSpatialIndexInstance();
        $index->setDistanceMetric($dist);
        $index->index($points);

        $idxs = $index->regionQuery($point, $eps);

        $this->assertEquals(
            $results,
            $idxs
        );
    }
}
