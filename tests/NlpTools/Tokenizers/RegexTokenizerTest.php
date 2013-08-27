<?php

namespace NlpTools\Tokenizers;

class RegexTokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test simple splitting patterns
     */
    public function testSplit()
    {
        // check split1
        $tok = new RegexTokenizer(array(
            "/\s+/"
        ));

        $tokens = $tok->tokenize("0 1 2 3 4 5 6 7 8 9");
        $this->assertCount(10, $tokens);
        $this->assertEquals("0123456789",implode("",$tokens));

        // check split2
        $tok = new RegexTokenizer(array(
            "/\n+/"
        ));

        $tokens = $tok->tokenize("0 1 2 3 4\n5 6 7 8 9");
        $this->assertCount(2, $tokens);
        $this->assertEquals("0 1 2 3 45 6 7 8 9",implode("",$tokens));

        $tokens = $tok->tokenize("0 1 2 3 4\n\n5 6 7 8 9");
        $this->assertCount(2, $tokens);
        $this->assertEquals("0 1 2 3 45 6 7 8 9",implode("",$tokens));

    }

    /**
     * Test a pattern that captures instead of splits
     */
    public function testMatches()
    {
        // check keep matches
        $tok = new RegexTokenizer(array(
            array("/(\s+)?(\w+)(\s+)?/",2)
        ));

        $tokens = $tok->tokenize("0 1 2 3 4 5 6 7 8 9");
        $this->assertCount(10, $tokens);
        $this->assertEquals("0123456789",implode("",$tokens));
    }

    /**
     * Test a pattern that firsts replaces all digits with themselves separated
     * by a space and then tokenizes on whitespace.
     */
    public function testReplace()
    {
        // check keep matches
        $tok = new RegexTokenizer(array(
            array("/\d/",'$0 '),
            WhitespaceTokenizer::PATTERN
        ));

        $tokens = $tok->tokenize("0123456789");
        $this->assertCount(10, $tokens);
        $this->assertEquals("0123456789",implode("",$tokens));
    }

    /**
     * Test a simple pattern meant to split the full stop from the last
     * word of a sentence.
     */
    public function testSplitWithManyPatterns()
    {
        $tok = new RegexTokenizer(array(
            WhitespaceTokenizer::PATTERN, 	// split on whitespace
            array("/([^\.])\.$/",'$1 .'),	// replace <word>. with <word><space>.
            "/ /"							// split on <space>
        ));

        // example text stolen from NLTK :-)
        $str = "Good muffins cost $3.88\nin New York.  Please buy me\ntwo of them.\n\nThanks.";

        $tokens = $tok->tokenize($str);
        $this->assertCount(17, $tokens);
        $this->assertEquals($tokens[3], "$3.88");
        $this->assertEquals($tokens[7], ".");
        $this->assertEquals($tokens[14], ".");
        $this->assertEquals($tokens[16], ".");
    }
}
