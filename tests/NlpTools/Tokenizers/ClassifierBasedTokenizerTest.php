<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

use NlpTools\Classifiers\EndOfSentenceRules;
use PHPUnit\Framework\TestCase;

class ClassifierBasedTokenizerTest extends TestCase
{
    public function testTokenizer(): void
    {
        $classifierBasedTokenizer = new ClassifierBasedTokenizer(
            new EndOfSentenceRules(),
            new WhitespaceTokenizer()
        );

        $text = "We are what we repeatedly do.
                Excellence, then, is not an act, but a habit.";

        $this->assertEquals(
            ["We are what we repeatedly do.", "Excellence, then, is not an act, but a habit."],
            $classifierBasedTokenizer->tokenize($text)
        );
    }
}
