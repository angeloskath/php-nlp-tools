<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class TverskyIndexTest extends TestCase
{
    /**
     * @param array<int, mixed> $A
     * @param array<int, mixed> $B
     */
    private function sim(array $A, array $B, float $a, int $b): float
    {
        $tverskyIndex = new TverskyIndex($a, $b);

        return $tverskyIndex->similarity($A, $B);
    }

    public function testTverskyIndex(): void
    {
        new TverskyIndex();

        $A = ["my", "name", "is", "john"];
        $B = ["my", "name", "is", "joe"];
        $C = [1, 2, 3];
        $D = [1, 2, 3, 4, 5, 6];
        $e = [];

        $this->assertEquals(
            1,
            $this->sim($A, $A, 0.5, 1),
            "The similarity of a set with itsself is 1"
        );

        $this->assertEquals(
            0,
            $this->sim($A, $e, 0.5, 2),
            "The similarity of any set with the empty set is 0"
        );

        $this->assertEquals(
            0.75,
            $this->sim($A, $B, 0.5, 1),
            "similarity({'my','name','is','john'},{'my','name','is','joe'}) = 0.75"
        );

        $this->assertEquals(
            0.5,
            $this->sim($C, $D, 0.5, 2),
            "similarity({1,2,3},{1,2,3,4,5,6}) = 0.5"
        );
    }
}
