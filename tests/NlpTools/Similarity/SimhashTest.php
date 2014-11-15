<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\ArrayFeatureVector;

class SimhashTest extends \PHPUnit_Framework_TestCase
{
    public function testSimhash()
    {
        $sim = new Simhash(64);

        $A = new ArrayFeatureVector(array(1,2,3));
        $B = new ArrayFeatureVector(array(1,2,3,4,5,6));
        $b = new ArrayFeatureVector(array(1,2,3,4,5));
        $e = new ArrayFeatureVector(array());

        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "Two identical sets should have the same hash therefore a similarity of 1"
        );

        $this->assertGreaterThan(
            $sim->similarity($A,$B),
            $sim->similarity($b,$B),
            "The more elements in common the more similar the two sets should be"
        );
    }

    public function testWeightedSets()
    {
        $sim = new Simhash(64);

        $A = new ArrayFeatureVector(array("a","a","a","b","b",));
        $B = new ArrayFeatureVector(array("a"=>3,"b"=>2));

        $this->assertEquals(
            1,
            $sim->similarity($A,$B),
            "The two sets are identical given that one is the weighted version of the other"
        );
    }
}
