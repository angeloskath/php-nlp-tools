<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class SimhashTest extends TestCase
{
    public function testSimhash(): void
    {
        $simhash = new Simhash(64);

        $A = [1, 2, 3];
        $B = [1, 2, 3, 4, 5, 6];
        $b = [1, 2, 3, 4, 5];

        $this->assertEquals(
            1,
            $simhash->similarity($A, $A),
            "Two identical sets should have the same hash therefore a similarity of 1"
        );

        $this->assertGreaterThan(
            $simhash->similarity($A, $B),
            $simhash->similarity($b, $B),
            "The more elements in common the more similar the two sets should be"
        );
    }

    public function testWeightedSets(): void
    {
        $simhash = new Simhash(64);

        $A = ["a", "a", "a", "b", "b"];
        $B = ["a" => 3, "b" => 2];

        $this->assertEquals(
            1,
            $simhash->similarity($A, $B),
            "The two sets are identical given that one is the weighted version of the other"
        );
    }
}
