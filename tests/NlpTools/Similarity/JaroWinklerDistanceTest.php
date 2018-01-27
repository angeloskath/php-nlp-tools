<?php

namespace NlpTools\Similarity;

class JaroWinklerDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testJaroWinklerDistance()
    {
        $dist = new JaroWinklerDistance();

        $A = "john";
        $A_arr = array("j","o","h","n");
        $e = "";
        $e_arr = array();

        $this->assertEquals(
            1,
            $dist->dist($A,$A),
            "Similar strings should equate to 1"
        );

        $this->assertEquals(
            1,
            $dist->dist($A_arr,$A_arr),
            "Similar arrays should equate to 1"
        );

        $this->assertEquals(
            0,
            $dist->dist($A,$e),
            "Comparing to an empty string should equate to 0"
        );

        $this->assertEquals(
            0,
            $dist->dist($A,$e_arr),
            "Comparing to an empty array should equate to 0"
        );
    }
}
