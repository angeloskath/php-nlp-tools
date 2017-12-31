<?php

namespace NlpTools\Similarity;

class JaroWinklerDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testJaroWinklerDistance()
    {
        $dist = new JaroWinklerDistance();

        $A = "john";
        $B = "shaw";
        $e = "";

        $this->assertEquals(
            1,
            $dist->dist($A,$A),
            "Similar strings should equate to 1"
        );

        $this->assertEquals(
            0,
            $dist->dist($A,$e),
            "Comparing to an empty string should equate to 0"
        );
    }
}
