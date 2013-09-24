<?php

namespace NlpTools\Tokenizers;

/**
 *
 * @author Dan Cardin
 */
class PennBankTokenizerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testTokenizer()
    {
        $tokenizer = new PennTreeBankTokenizer();
        $tokens = $tokenizer->tokenize("Good muffins cost $3.88\nin New York.  Please buy me\ntwo of them.\nThanks.");
        $this->assertCount(16, $tokens);
    }

    public function testTokenizer2()
    {
        $tokenizer = new PennTreeBankTokenizer();
        $this->assertCount(7, $tokenizer->tokenize("They'll save and invest more."));
    }
    
    public function testTokenizer3()
    {
        $tokenizer = new PennTreeBankTokenizer();
        $this->assertCount(4, $tokenizer->tokenize("I'm some text"));
    }    

}
