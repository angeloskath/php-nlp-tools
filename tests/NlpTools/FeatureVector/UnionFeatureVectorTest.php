<?php

namespace NlpTools\FeatureVector;

class UnionFeatureVectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $fv1 = new ArrayFeatureVector(array("f1","f2","f3"));
        $fv2 = new ArrayFeatureVector(array("f4","f5","f5"));

        try {
            $fv = new UnionFeatureVector(array(array("f1","f2")));
            $this->fail("Only FeatureVectors should be unioned");
        } catch (\InvalidArgumentException $e) {
        }

        $fv = new UnionFeatureVector(array($fv1, $fv2));
        $this->assertCount(5, $fv);
        $this->assertEquals(
            array(
                "f1"=>1,
                "f2"=>1,
                "f3"=>1,
                "f4"=>1,
                "f5"=>2
            ),
            iterator_to_array($fv)
        );

        $this->assertTrue(isset($fv["f1"]));
        $this->assertFalse(isset($fv["f11"]));
        $this->assertEquals(
            2,
            $fv["f5"]
        );
    }
}
