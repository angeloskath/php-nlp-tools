<?php

namespace NlpTools\Similarity;

use NlpTools\FeatureVector\ArrayFeatureVector;

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
        $A = new ArrayFeatureVector(array("x"=>"A","y"=>"B","z"=>"C","w"=>"D","q"=>"E"));
        $B = new ArrayFeatureVector(array("x"=>"F","y"=>"G","z"=>"H","w"=>"I","q"=>"J"));
        $C = new ArrayFeatureVector(array("x"=>"1","y"=>"0","z"=>"1","w"=>"0","q"=>"1"));
        $D = new ArrayFeatureVector(array("x"=>"1","y"=>"1","z"=>"1","w"=>"1","q"=>"1"));

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
