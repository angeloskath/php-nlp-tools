<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class CosineSimilarityTest extends TestCase
{
    public function testSetSimilarity(): void
    {
        $cosineSimilarity = new CosineSimilarity();

        $A = [1, 2, 3];
        $A_times_2 = [1, 2, 3, 1, 2, 3];
        $B = [1, 2, 3, 4, 5, 6];

        $this->assertEquals(
            1,
            (int) $cosineSimilarity->similarity($A, $A),
            "The cosine similarity of a set/vector with itsself should be 1"
        );

        $this->assertEquals(
            1,
            (int) $cosineSimilarity->similarity($A, $A_times_2),
            "The cosine similarity of a vector with a linear combination of itsself should be 1"
        );

        $this->assertEquals(
            0,
            (int) ($cosineSimilarity->similarity($A, $B) - $cosineSimilarity->similarity($A_times_2, $B)),
            "Parallel vectors should have the same angle with any vector B"
        );
    }

    public function testProducedAngles(): void
    {
        $cosineSimilarity = new CosineSimilarity();

        $ba = [1, 1, 2, 2, 2, 2]; // ba = (2,4)
        $bc = [1, 1, 1, 2, 2]; // bc = (3,2)
        $bba = ['a' => 2, 'b' => 4];
        $bbc = ['a' => 3, 'b' => 2];
        $ba_to_bc = round(cos(0.5191461142), 8); // approximately 30 deg

        $this->assertEquals(
            $ba_to_bc,
            round($cosineSimilarity->similarity($ba, $bc), 8)
        );

        $this->assertEquals(
            $ba_to_bc,
            round($cosineSimilarity->similarity($bba, $bbc), 8)
        );
    }

    public function testInvalidArgumentException(): void
    {
        $cosineSimilarity = new CosineSimilarity();
        $a = [1];
        $zero = [];
        try {
            $cosineSimilarity->similarity(
                $a,
                $zero
            );
            $this->fail("Cosine similarity with the zero vector should trigger an exception");
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $this->assertEquals(
                "Vector \$B is the zero vector",
                $invalidArgumentException->getMessage()
            );
        }

        try {
            $cosineSimilarity->similarity(
                $zero,
                $a
            );
            $this->fail("Cosine similarity with the zero vector should trigger an exception");
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $this->assertEquals(
                "Vector \$A is the zero vector",
                $invalidArgumentException->getMessage()
            );
        }
    }
}
