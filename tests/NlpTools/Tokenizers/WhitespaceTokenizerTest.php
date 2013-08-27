<?php

namespace NlpTools\Tokenizers;

class WhitespaceTokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenizerOnAscii()
    {
        $tok = new WhitespaceTokenizer();

        $s = "This is a simple space delimited string
        with new lines and many     spaces between the words.
        Also	tabs	tabs	tabs	tabs";
        $tokens = array('This','is','a','simple','space','delimited','string',
        'with','new','lines','and','many','spaces','between','the','words.',
        'Also','tabs','tabs','tabs','tabs');

        $this->assertEquals(
            $tokens,
            $tok->tokenize($s)
        );
    }

    public function testTokenizerOnUtf8()
    {
        $tok = new WhitespaceTokenizer();

        $s = "Ελληνικό κείμενο για παράδειγμα utf-8 χαρακτήρων";
        $tokens = array('Ελληνικό','κείμενο','για','παράδειγμα','utf-8','χαρακτήρων');
        // test tokenization of multibyte non-whitespace characters
        $this->assertEquals(
            $tokens,
            $tok->tokenize($s)
        );

        $s = "Here exists non-breaking space   ";
        $tokens = array('Here','exists','non-breaking','space');
        // test tokenization of multibyte whitespace
        $this->assertEquals(
            $tokens,
            $tok->tokenize($s)
        );
    }
}
