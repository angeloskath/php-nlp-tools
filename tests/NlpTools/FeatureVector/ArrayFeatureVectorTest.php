<?php

namespace NlpTools\FeatureVector;

class ArrayFeatureVectorTest extends \PHPUnit_Framework_TestCase
{
    public function testFeatureVectorCreation()
    {
        $fv = new ArrayFeatureVector();
        $this->assertCount(0, $fv);

        $fv = new ArrayFeatureVector(array(
            "f1"=>1,
            "f2"=>2,
            "f3"=>3
        ));
        $this->assertCount(3, $fv);
        $this->assertEquals(
            array(
                "f1"=>1,
                "f2"=>2,
                "f3"=>3
            ),
            iterator_to_array($fv)
        );

        $fv = new ArrayFeatureVector(array(
            "f1",
            "f2","f2",
            "f3","f3","f3"
        ));
        $this->assertCount(3, $fv);
        $this->assertEquals(
            array(
                "f1"=>1,
                "f2"=>2,
                "f3"=>3
            ),
            iterator_to_array($fv)
        );
    }

    public function testGet()
    {
        $fv = new ArrayFeatureVector(array(
            "f1",
            "f2","f2",
            "f3","f3","f3"
        ));

        $this->assertEquals(1, $fv["f1"]);
        $this->assertEquals(2, $fv["f2"]);
        $this->assertEquals(3, $fv["f3"]);
        $this->assertTrue(isset($fv["f3"]));
        $this->assertFalse(isset($fv["f4"]));
    }
}
