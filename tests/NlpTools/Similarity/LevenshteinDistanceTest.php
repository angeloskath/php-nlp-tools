<?php

namespace NlpTools\Similarity;

class LevenshteinDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testLevenshteinDistance()
    {
        $dist = new LevenshteinDistance();

        $A = "kitten";
        $B = "sitting";

        $this->assertEquals(
            3,
            $dist->dist($A,$B),
            "kitten ~ sitting have a levenshtein distance = 3"
        );

        $this->assertEquals(
            0,
            $dist->dist($A,$a),
            "same words have a levenshtein distance = 0"
        );
    }
}
