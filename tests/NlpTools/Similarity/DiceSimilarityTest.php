<?php

namespace NlpTools\Similarity;

class DiceSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testDiceSimilarity()
    {
        $sim = new DiceSimilarity();

        $A = array("my","name","is","john");
        $B = array("my","name","is","joe");
        $e = array();

        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "The similarity of a set with itsself is 1"
        );

        $this->assertEquals(
            0,
            $sim->similarity($A,$e),
            "The similarity of any set with the empty set is 0"
        );

        $this->assertEquals(
            0.75,
            $sim->similarity($A,$B),
            "similarity({'my','name','is','john'},{'my','name','is','joe'}) = 0.75"
        );
    }
}
