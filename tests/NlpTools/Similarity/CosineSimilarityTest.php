<?php

namespace NlpTools\Similarity;

class CosineSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testSetSimilarity()
    {
        $sim = new CosineSimilarity();

        $A = array(1,2,3);
        $A_times_2 = array(1,2,3,1,2,3);
        $B = array(1,2,3,4,5,6);

        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "The cosine similarity of a set/vector with itsself should be 1"
        );

        $this->assertEquals(
            1,
            $sim->similarity($A,$A_times_2),
            "The cosine similarity of a vector with a linear combination of itsself should be 1"
        );

        $this->assertEquals(
            0,
            $sim->similarity($A,$B)-$sim->similarity($A_times_2,$B),
            "Parallel vectors should have the same angle with any vector B"
        );
    }

    public function testProducedAngles()
    {
        $sim = new CosineSimilarity();

        $ba = array(1,1,2,2,2,2); // ba = (2,4)
        $bc = array(1,1,1,2,2); // bc = (3,2)
        $bba = array('a'=>2,'b'=>4);
        $bbc = array('a'=>3,'b'=>2);
        $ba_to_bc = cos(0.5191461142); // approximately 30 deg

        $this->assertEquals(
            $ba_to_bc,
            $sim->similarity($ba,$bc)
        );

        $this->assertEquals(
            $ba_to_bc,
            $sim->similarity($bba,$bbc)
        );
    }

    public function testInvalidArgumentException()
    {
        $sim = new CosineSimilarity();
        $a = array(1);
        $zero = array();
        try {
            $sim->similarity(
                $a,
                $zero
            );
            $this->fail("Cosine similarity with the zero vector should trigger an exception");
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                "Vector \$B is the zero vector",
                $e->getMessage()
            );
        }
        try {
            $sim->similarity(
                $zero,
                $a
            );
            $this->fail("Cosine similarity with the zero vector should trigger an exception");
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                "Vector \$A is the zero vector",
                $e->getMessage()
            );
        }
    }
}
