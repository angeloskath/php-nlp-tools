<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

use PHPUnit\Framework\TestCase;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;

class WhitespaceAndPunctuationTokenizerTest extends TestCase
{
    public function testTokenizerOnAscii(): void
    {
        $whitespaceAndPunctuationTokenizer = new WhitespaceAndPunctuationTokenizer();

        $s = "This is a simple space delimited string
        with new lines and many     spaces between the words.
        Also	tabs	tabs	tabs	tabs";
        $tokens = ['This', 'is', 'a', 'simple', 'space', 'delimited', 'string', 'with', 'new', 'lines', 'and', 'many', 'spaces', 'between', 'the', 'words', '.', 'Also', 'tabs', 'tabs', 'tabs', 'tabs'];

        $this->assertEquals(
            $tokens,
            $whitespaceAndPunctuationTokenizer->tokenize($s)
        );
    }

    public function testTokenizerOnUtf8(): void
    {
        $whitespaceAndPunctuationTokenizer = new WhitespaceAndPunctuationTokenizer();

        $s = "Ελληνικό κείμενο για παράδειγμα utf-8 χαρακτήρων";
        $tokens = ['Ελληνικό', 'κείμενο', 'για', 'παράδειγμα', 'utf', '-', '8', 'χαρακτήρων'];
        // test tokenization of multibyte non-whitespace characters
        $this->assertEquals(
            $tokens,
            $whitespaceAndPunctuationTokenizer->tokenize($s)
        );

        $s = "Here exists non-breaking space   ";
        $tokens = ['Here', 'exists', 'non', '-', 'breaking', 'space'];
        // test tokenization of multibyte whitespace
        $this->assertEquals(
            $tokens,
            $whitespaceAndPunctuationTokenizer->tokenize($s)
        );
    }
}
