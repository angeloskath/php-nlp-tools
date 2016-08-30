<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\FeatureVector\ArrayFeatureVector;
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
            new ArrayFeatureVector(array('x'=>1, 'y'=>2)),
            new ArrayFeatureVector(array('x'=>1, 'y'=>2)),
            new ArrayFeatureVector(array('x'=>1, 'y'=>2)),
            new ArrayFeatureVector(array('x'=>1, 'y'=>2)),
            new ArrayFeatureVector(array('x'=>1, 'y'=>2)),
            new ArrayFeatureVector(array('x'=>1, 'y'=>2)),
            new ArrayFeatureVector(array('x'=>2, 'y'=>2))
        );
        $index->index($points);
        $point = new ArrayFeatureVector(array('x'=>1, 'y'=>2));

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
        for ($i=0; $i<50000; $i++) {
            $points[] = new ArrayFeatureVector(array(
                'a'=>mt_rand()/mt_getrandmax(),
                'b'=>mt_rand()/mt_getrandmax(),
                'c'=>mt_rand()/mt_getrandmax(),
                'd'=>mt_rand()/mt_getrandmax(),
                'e'=>mt_rand()/mt_getrandmax(),
                'f'=>mt_rand()/mt_getrandmax(),
                'g'=>mt_rand()/mt_getrandmax(),
                'h'=>mt_rand()/mt_getrandmax(),
                'j'=>mt_rand()/mt_getrandmax(),
                'k'=>mt_rand()/mt_getrandmax()
            ));
        }
        $queries = array_rand($points, 100);

        $baseline->index($points);
        $resultsBaseline = [];
        $start = microtime(true);
        foreach ($queries as $query) {
            $resultsBaseline[] = $baseline->kNearestNeighbors($points[$query], 5);
        }
        $baselineDuration = microtime(true) - $start;
        $resultsBaseline = call_user_func_array("array_merge", $resultsBaseline);
        sort($resultsBaseline);

        $start = microtime(true);
        $index->index($points);
        $buildTime = microtime(true) - $start;
        $results = [];
        $start = microtime(true);
        foreach ($queries as $query) {
            $results[] = $index->kNearestNeighbors($points[$query], 5);
        }
        $duration = microtime(true) - $start;
        $results = call_user_func_array("array_merge", $results);
        sort($results);

        $this->assertEquals(
            $resultsBaseline,
            $results
        );
        $this->assertLessThan(
            $baselineDuration,
            $duration + $buildTime
        );
    }

    /**
     * @group Slow
     * @group VerySlow
     */
    public function testSubsquareScaling()
    {
        $index = $this->getSpatialIndexInstance();
        $index->setDistanceMetric(new Euclidean());

        // require too much memory for some systems
        $sizes = array(10000, 20000, 40000/*, 80000, 160000*/);
        $duration = array();
        $points = array();
        foreach ($sizes as $size) {
            for ($i=count($points); $i<$size; $i++) {
                $points[] = new ArrayFeatureVector(array(
                    'x'=>mt_rand()/mt_getrandmax(),
                    'y'=>mt_rand()/mt_getrandmax()
                ));
            }
            $index->index($points);
            $start = microtime(true);
            for ($i=0; $i<10; $i++) {
                $index->kNearestNeighbors($points[mt_rand() % count($points)], 10);
            }
            $duration[$size] = microtime(true) - $start;
        }

        // assert sub square complexity
        for ($i=min($sizes); $i<max($sizes); $i *= 2) {
            $this->assertTrue(
                $duration[2*$i]/$duration[$i] < 4
            );
        }
    }
}
