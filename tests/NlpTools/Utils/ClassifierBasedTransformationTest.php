<?php

namespace NlpTools\Utils;

use NlpTools\Classifiers\ClassifierInterface;
use NlpTools\Documents\DocumentInterface;

class ClassifierBasedTransformationTest extends \PHPUnit_Framework_TestCase implements ClassifierInterface
{
    public function classify(array $classes, DocumentInterface $d)
    {
        return $classes[$d->getDocumentData() % count($classes)];
    }

    public function testEvenAndOdd()
    {
        $stubEven = $this->getMock("NlpTools\\Utils\\TransformationInterface");
        $stubEven->expects($this->any())
            ->method('transform')
            ->will($this->returnValue('even'));
        $stubOdd = $this->getMock("NlpTools\\Utils\\TransformationInterface");
        $stubOdd->expects($this->any())
            ->method('transform')
            ->will($this->returnValue('odd'));

        $transform = new ClassifierBasedTransformation($this);
        $transform->register("even", $stubEven);
        $transform->register("odd", $stubOdd);

        $this->assertEquals(
            "odd",
            $transform->transform(3)
        );
        $this->assertEquals(
            "even",
            $transform->transform(4)
        );
    }
}
