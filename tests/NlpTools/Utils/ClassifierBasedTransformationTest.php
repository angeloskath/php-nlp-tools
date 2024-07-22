<?php

declare(strict_types=1);

namespace NlpTools\Utils;

use NlpTools\Classifiers\ClassifierInterface;
use NlpTools\Documents\DocumentInterface;
use NlpTools\Utils\TransformationInterface;
use PHPUnit\Framework\TestCase;

class ClassifierBasedTransformationTest extends TestCase implements ClassifierInterface
{
    /**
     * @param array<int, mixed> $classes
     */
    public function classify(array $classes, DocumentInterface $document): string
    {
        return $classes[$document->getDocumentData() % count($classes)];
    }

    public function testEvenAndOdd(): void
    {
        $stubEven = $this->createMock(TransformationInterface::class);
        $stubEven->expects($this->any())
            ->method('transform')
            ->willReturn('even');
        $stubOdd = $this->createMock(TransformationInterface::class);
        $stubOdd->expects($this->any())
            ->method('transform')
            ->willReturn('odd');

        $classifierBasedTransformation = new ClassifierBasedTransformation($this);
        $classifierBasedTransformation->register("even", $stubEven);
        $classifierBasedTransformation->register("odd", $stubOdd);

        $this->assertEquals(
            "odd",
            $classifierBasedTransformation->transform('3')
        );
        $this->assertEquals(
            "even",
            $classifierBasedTransformation->transform('4')
        );
    }
}
