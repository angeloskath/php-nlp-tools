<?php

declare(strict_types=1);

namespace NlpTools\Utils\Normalizers;

use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    public function testNormalizer(): void
    {
        $normalizer = Normalizer::factory();
        $greek = Normalizer::factory("Greek");

        $this->assertEquals(
            explode(" ", "ο μορφωμενοσ διαφερει απο τον αμορφωτο οσο ο ζωντανοσ απο τον νεκρο"),
            $greek->normalizeAll(
                explode(" ", "Ο μορφωμένος διαφέρει από τον αμόρφωτο όσο ο ζωντανός από τον νεκρό")
            )
        );

        $this->assertEquals(
            explode(" ", "ο μορφωμένος διαφέρει από τον αμόρφωτο όσο ο ζωντανός από τον νεκρό"),
            $normalizer->normalizeAll(
                explode(" ", "Ο μορφωμένος διαφέρει από τον αμόρφωτο όσο ο ζωντανός από τον νεκρό")
            )
        );

        $this->assertEquals(
            explode(" ", "when a father gives to his son both laugh when a son gives to his father both cry"),
            $normalizer->normalizeAll(
                explode(" ", "When a father gives to his son both laugh when a son gives to his father both cry")
            )
        );
    }
}
