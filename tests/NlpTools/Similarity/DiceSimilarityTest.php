<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class DiceSimilarityTest extends TestCase
{
    public function testDiceSimilarity(): void
    {
        $diceSimilarity = new DiceSimilarity();

        $A = ["my", "name", "is", "john"];
        $B = ["my", "name", "is", "joe"];
        $e = [];

        $this->assertEquals(
            1,
            $diceSimilarity->similarity($A, $A),
            "The similarity of a set with itsself is 1"
        );

        $this->assertEquals(
            0,
            $diceSimilarity->similarity($A, $e),
            "The similarity of any set with the empty set is 0"
        );

        $this->assertEquals(
            0.75,
            $diceSimilarity->similarity($A, $B),
            "similarity({'my','name','is','john'},{'my','name','is','joe'}) = 0.75"
        );
    }
}
