<?php

namespace NlpTools\Utils\Normalizers;

class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizer()
    {
        $english = Normalizer::factory();
        $greek = Normalizer::factory("Greek");

        $this->assertEquals(
            explode(" ","ο μορφωμενοσ διαφερει απο τον αμορφωτο οσο ο ζωντανοσ απο τον νεκρο"),
            $greek->normalizeAll(
                explode(" ","Ο μορφωμένος διαφέρει από τον αμόρφωτο όσο ο ζωντανός από τον νεκρό")
            )
        );

        $this->assertEquals(
            explode(" ","ο μορφωμένος διαφέρει από τον αμόρφωτο όσο ο ζωντανός από τον νεκρό"),
            $english->normalizeAll(
                explode(" ","Ο μορφωμένος διαφέρει από τον αμόρφωτο όσο ο ζωντανός από τον νεκρό")
            )
        );

        $this->assertEquals(
            explode(" ","when a father gives to his son both laugh when a son gives to his father both cry" ),
            $english->normalizeAll(
                explode(" ","When a father gives to his son both laugh when a son gives to his father both cry" )
            )
        );
    }
}
