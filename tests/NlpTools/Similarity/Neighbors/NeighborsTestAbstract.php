<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\Distance;
use NlpTools\Similarity\Euclidean;

abstract class NeighborsTestAbstract extends \PHPUnit_Framework_TestCase
{
    abstract protected function getSpatialIndexInstance();

    public function provideRegionQueries()
    {
        $dist = new Euclidean();
        $points = array(
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
        return array(
            array($dist, $points, array('x'=>3,'y'=>3), 1.1, array(3,13))
        );
    }

    /**
     * @dataProvider provideRegionQueries
     */
    public function testRegionQueries(Distance $dist, $points, $point, $eps, $results)
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
