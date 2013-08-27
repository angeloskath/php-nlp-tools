<?php

namespace NlpTools\Tokenizers;

use NlpTools\Classifiers\EndOfSentenceRules;

class ClassifierBasedTokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenizer()
    {
        $tok = new ClassifierBasedTokenizer(
            new EndOfSentenceRules(),
            new WhitespaceTokenizer()
        );

        $text = "We are what we repeatedly do.
                Excellence, then, is not an act, but a habit.";

        $this->assertEquals(
            array(
                "We are what we repeatedly do.",
                "Excellence, then, is not an act, but a habit."
            ),
            $tok->tokenize($text)
        );
    }
}
