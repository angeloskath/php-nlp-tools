<?php

namespace NlpTools\Similarity;

class TverskyIndexTest extends \PHPUnit_Framework_TestCase
{
    private function sim($A, $B, $a, $b)
    {
        $sim = new TverskyIndex($a, $b);

        return $sim->similarity($A, $B);
    }

    public function testTverskyIndex()
    {
        $sim = new TverskyIndex();

        $A = array("my","name","is","john");
        $B = array("my","name","is","joe");
        $C = array(1,2,3);
        $D = array(1,2,3,4,5,6);
        $e = array();

        $this->assertEquals(
            1,
            $this->sim($A,$A, 0.5, 1),
            "The similarity of a set with itsself is 1"
        );

        $this->assertEquals(
            0,
            $this->sim($A,$e, 0.5, 2),
            "The similarity of any set with the empty set is 0"
        );

        $this->assertEquals(
            0.75,
            $this->sim($A,$B, 0.5, 1),
            "similarity({'my','name','is','john'},{'my','name','is','joe'}) = 0.75"
        );

        $this->assertEquals(
            0.5,
            $this->sim($C,$D, 0.5, 2),
            "similarity({1,2,3},{1,2,3,4,5,6}) = 0.5"
        );
    }
}
