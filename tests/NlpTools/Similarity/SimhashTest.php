<?php

namespace NlpTools\Similarity;

class SimhashTest extends \PHPUnit_Framework_TestCase
{
    public function testSimhash()
    {
        $sim = new Simhash(64);

        $A = array(1,2,3);
        $B = array(1,2,3,4,5,6);
        $b = array(1,2,3,4,5);
        $e = array();

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
}
