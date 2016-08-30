<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\FeatureVector\ArrayFeatureVector;
use NlpTools\Similarity\DistanceInterface;
use NlpTools\Similarity\Euclidean;

abstract class NeighborsTestAbstract extends \PHPUnit_Framework_TestCase
{
    abstract protected function getSpatialIndexInstance();

    protected function getPoints()
    {
        return array(
            new ArrayFeatureVector(array('x'=>0,'y'=>0)), // 0
            new ArrayFeatureVector(array('x'=>1,'y'=>1)), // 1
            new ArrayFeatureVector(array('x'=>2,'y'=>2)), // 2
            new ArrayFeatureVector(array('x'=>3,'y'=>3)), // 3
            new ArrayFeatureVector(array('x'=>4,'y'=>2)), // 4
            new ArrayFeatureVector(array('x'=>5,'y'=>1)), // 5
            new ArrayFeatureVector(array('x'=>6,'y'=>0)), // 6
            new ArrayFeatureVector(array('x'=>5,'y'=>0)), // 7
            new ArrayFeatureVector(array('x'=>4,'y'=>0)), // 8
            new ArrayFeatureVector(array('x'=>3,'y'=>0)), // 9
            new ArrayFeatureVector(array('x'=>2,'y'=>0)), // 10
            new ArrayFeatureVector(array('x'=>1,'y'=>0)), // 11
            new ArrayFeatureVector(array('x'=>2,'y'=>1)), // 12
            new ArrayFeatureVector(array('x'=>3,'y'=>2)), // 13
            new ArrayFeatureVector(array('x'=>4,'y'=>1))  // 14
        );
    }

    public function provideRegionQueries()
    {
        $dist = new Euclidean();
        $points = $this->getPoints();
        return array(
            array($dist, $points, new ArrayFeatureVector(array('x'=>3,'y'=>3)), 1.1, array(3,13))
        );
    }

    public function provideKNNQueries()
    {
        $dist = new Euclidean();
        $points = $this->getPoints();
        return array(
            array($dist, $points, new ArrayFeatureVector(array('x'=>3, 'y'=>3)), 1, array(3)),
            array($dist, $points, new ArrayFeatureVector(array('x'=>3, 'y'=>3)), 2, array(3,13))
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
        sort($idxs);

        $this->assertEquals(
            $results,
            $idxs
        );
    }
}
