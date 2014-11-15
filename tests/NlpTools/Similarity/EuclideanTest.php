<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\ArrayFeatureVector;

class EuclideanTest extends \PHPUnit_Framework_TestCase
{
    public function pointsDistancesProvider()
    {
        return array(
            array(array("x"=>1,"y"=>1), array("x"=>2,"y"=>2), sqrt(2))
        );
    }

    /**
     * @dataProvider pointsDistancesProvider
     */
    public function testEuclideanDistances($a, $b, $d)
    {
        $sim = new Euclidean();

        $this->assertEquals(
            $d,
            $sim->dist(new ArrayFeatureVector($a), new ArrayFeatureVector($b))
        );
    }

    public function testInvalidArguments()
    {
        $sim = new Euclidean();

        try {
            $sim->dist(
                array(1,2,3),
                array(4,5,6)
            );
            $this->fail("Euclidean should only accept FeatureVector instances");
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                "Euclidean accepts only FeatureVector instances",
                $e->getMessage()
            );
        }
    }
}
