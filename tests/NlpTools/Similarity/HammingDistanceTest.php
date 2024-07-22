<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

use PHPUnit\Framework\TestCase;

class HammingDistanceTest extends TestCase
{
    public function testHammingDistance(): void
    {
        $hammingDistance = new HammingDistance();

        $A = "ABCDE";
        $B = "FGHIJ";
        $C = "10101";
        $D = "11111";

        $a = [$A];
        $b = [$B];
        $c = [$C];
        $d = [$D];

        $this->assertEquals(
            max(strlen($A), strlen($B)),
            $hammingDistance->dist($a, $b),
            "Two completely dissimilar strings should have distance equal to max(strlen(\$A),strlen(\$B))"
        );

        $this->assertEquals(
            2,
            $hammingDistance->dist($c, $d),
            "10101 ~ 11111 have a hamming distance = 2"
        );
    }
}
