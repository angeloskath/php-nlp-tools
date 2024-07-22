<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class OverlapCoefficientTest extends TestCase
{
    public function testOverlapCoefficient(): void
    {
        $overlapCoefficient = new OverlapCoefficient();

        $A = ["my", "name", "is", "john"];
        $B = ["your", "name", "is", "joe"];
        $e = [];

        $this->assertEquals(
            1,
            $overlapCoefficient->similarity($A, $A),
            "The similarity of a set with itsself is 1"
        );

        $this->assertEquals(
            0,
            $overlapCoefficient->similarity($A, $e),
            "The similarity of any set with the empty set is 0"
        );

        $this->assertEquals(
            0.5,
            $overlapCoefficient->similarity($A, $B),
            "similarity({'my','name','is','john'},{'your','name','is','joe'}) = 0.5"
        );
    }
}
