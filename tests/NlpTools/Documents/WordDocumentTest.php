<?php

namespace NlpTools\Documents;

/**
 * TODO: Add checks for the edges of the token list
 */
class WordDocumentTest extends \PHPUnit_Framework_TestCase
{
    protected $tokens;

    public function __construct()
    {
        $this->tokens = array("The","quick","brown","fox","jumped","over","the","lazy","dog");
    }

    /**
     * Test that the WordDocument correctly represents the ith token
     */
    public function testTokenSelection()
    {
        foreach ($this->tokens as $i=>$t) {
            // no context
            $doc = new WordDocument($this->tokens, $i, 0);
            list($w,$prev,$next) = $doc->getDocumentData();

            $this->assertEquals(
                $t,
                $w,
                "The {$i}th token should be $t not $w"
            );

            // no context means prev,next are empty
            $this->assertCount(
                0,
                $prev
            );
            $this->assertCount(
                0,
                $next
            );
        }
    }

    /**
     * Start with the 5th word and increase the amount of context
     * until it reaches the edges of the token list. Check the
     * previous tokens.
     */
    public function testPrevContext()
    {
        for ($i=0;$i<5;$i++) {
            $doc = new WordDocument($this->tokens, 4, $i);
            list($_,$prev,$_) = $doc->getDocumentData();

            $this->assertCount(
                $i,
                $prev,
                "With $i words context prev should be $i words long"
            );
            for (
                $j=3,$y=$i-1;
                $j>=4-$i;
                $y--,$j--) {
                $this->assertEquals(
                    $this->tokens[$j],
                    $prev[$y]
                );
            }
        }
    }

    /**
     * Start with the 5th word and increase the amount of context
     * until it reaches the edges of the token list. Check the
     * next tokens.
     */
    public function testNextContext()
    {
        for ($i=0;$i<5;$i++) {
            $doc = new WordDocument($this->tokens, 4, $i);
            list($_,$_,$next) = $doc->getDocumentData();

            $this->assertCount(
                $i,
                $next,
                "With $i words context next should be $i words long"
            );
            for ($j=5; $j<5+$i; $j++) {
                $this->assertEquals(
                    $this->tokens[$j],
                    $next[$j-5]
                );
            }
        }
    }
}
