<?php

namespace NlpTools\Similarity;

class HammingDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testHammingDistance()
    {
        $dist = new HammingDistance();

        $A = "ABCDE";
        $B = "FGHIJ";
        $C = "10101";
        $D = "11111";

        $this->assertEquals(
            max(strlen($A),strlen($B)),
            $dist->dist($A,$B),
            "Two completely dissimilar strings should have distance equal to max(strlen(\$A),strlen(\$B))"
        );

        $this->assertEquals(
            2,
            $dist->dist($C,$D),
            "10101 ~ 11111 have a hamming distance = 2"
        );
    }

    public function testHammingInArrays()
    {
        $dist = new HammingDistance();
        $A = array("A","B","C","D","E");
        $B = array("F","G","H","I","J");
        $C = array("1","0","1","0","1");
        $D = array("1","1","1","1","1");

        $this->assertEquals(
            max(count($A),count($B)),
            $dist->dist($A,$B),
            "Two completely dissimilar sets should have distance equal to max(count(\$A),count(\$B))"
        );

        $this->assertEquals(
            2,
            $dist->dist($C,$D),
            "10101 ~ 11111 have a hamming distance = 2"
        );
    }

    public function testHammingInArraysVsStrings()
    {
        
        $dist = new HammingDistance();
        $A = "ABCDE";
        $B = array("F","G","H","I","J");
        $C = "10101";
        $D = array("1","1","1","1","1");

        $this->assertEquals(
            max(count($A),count($B)),
            $dist->dist($A,$B),
            "Two completely dissimilar sets should have distance equal to max(count(\$A),count(\$B))"
        );

        $this->assertEquals(
            2,
            $dist->dist($C,$D),
            "10101 ~ 11111 have a hamming distance = 2"
        );
    }
}
