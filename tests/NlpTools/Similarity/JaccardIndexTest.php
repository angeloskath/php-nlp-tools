<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class JaccardIndexTest extends TestCase
{
    public function testJaccardIndex(): void
    {
        $jaccardIndex = new JaccardIndex();

        $A = [1, 2, 3];
        $B = [1, 2, 3, 4, 5, 6];
        $e = [];

        $this->assertEquals(
            1,
            $jaccardIndex->similarity($A, $A),
            "The similarity of a set with itsself is 1"
        );

        $this->assertEquals(
            0,
            $jaccardIndex->similarity($A, $e),
            "The similarity of any set with the empty set is 0"
        );

        $this->assertEquals(
            0.5,
            $jaccardIndex->similarity($A, $B),
            "J({1,2,3},{1,2,3,4,5,6}) = 0.5"
        );
    }
}
