<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

use PHPUnit\Framework\TestCase;

class RegexTokenizerTest extends TestCase
{
    /**
     * Test simple splitting patterns
     */
    public function testSplit(): void
    {
        // check split1
        $tok = new RegexTokenizer(["/\s+/"]);

        $tokens = $tok->tokenize("0 1 2 3 4 5 6 7 8 9");
        $this->assertCount(10, $tokens);
        $this->assertEquals("0123456789", implode("", $tokens));

        // check split2
        $tok = new RegexTokenizer(["/\n+/"]);

        $tokens = $tok->tokenize("0 1 2 3 4\n5 6 7 8 9");
        $this->assertCount(2, $tokens);
        $this->assertEquals("0 1 2 3 45 6 7 8 9", implode("", $tokens));

        $tokens = $tok->tokenize("0 1 2 3 4\n\n5 6 7 8 9");
        $this->assertCount(2, $tokens);
        $this->assertEquals("0 1 2 3 45 6 7 8 9", implode("", $tokens));
    }

    /**
     * Test a pattern that captures instead of splits
     */
    public function testMatches(): void
    {
        // check keep matches
        $regexTokenizer = new RegexTokenizer([["/(\s+)?(\w+)(\s+)?/", 2]]);

        $tokens = $regexTokenizer->tokenize("0 1 2 3 4 5 6 7 8 9");
        $this->assertCount(10, $tokens);
        $this->assertEquals("0123456789", implode("", $tokens));
    }

    /**
     * Test a pattern that firsts replaces all digits with themselves separated
     * by a space and then tokenizes on whitespace.
     */
    public function testReplace(): void
    {
        // check keep matches
        $regexTokenizer = new RegexTokenizer([["/\d/", '$0 '], WhitespaceTokenizer::PATTERN]);

        $tokens = $regexTokenizer->tokenize("0123456789");
        $this->assertCount(10, $tokens);
        $this->assertEquals("0123456789", implode("", $tokens));
    }

    /**
     * Test a simple pattern meant to split the full stop from the last
     * word of a sentence.
     */
    public function testSplitWithManyPatterns(): void
    {
        $regexTokenizer = new RegexTokenizer([
            WhitespaceTokenizer::PATTERN,
            // split on whitespace
            ["/([^\.])\.$/", '$1 .'],
            // replace <word>. with <word><space>.
            "/ /",
        ]);

        // example text stolen from NLTK :-)
        $str = "Good muffins cost $3.88\nin New York.  Please buy me\ntwo of them.\n\nThanks.";

        $tokens = $regexTokenizer->tokenize($str);
        $this->assertCount(17, $tokens);
        $this->assertEquals($tokens[3], "$3.88");
        $this->assertEquals($tokens[7], ".");
        $this->assertEquals($tokens[14], ".");
        $this->assertEquals($tokens[16], ".");
    }
}
