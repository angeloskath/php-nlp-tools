<?php

namespace NlpTools\Similarity;

class JaccardIndexTest extends \PHPUnit_Framework_TestCase
{
    public function testJaccardIndex()
    {
        $sim = new JaccardIndex();

        $A = array(1,2,3);
        $B = array(1,2,3,4,5,6);
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
            0.5,
            $sim->similarity($A,$B),
            "J({1,2,3},{1,2,3,4,5,6}) = 0.5"
        );
    }
}
