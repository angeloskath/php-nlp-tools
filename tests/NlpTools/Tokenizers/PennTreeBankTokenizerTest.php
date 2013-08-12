<?php

namespace Tests\NlpTools\Tokenizers;

use NlpTools\Tokenizers\PennTreeBankTokenizer;

/**
 * 
 * @author yooper
 */
class PennBankTokenizerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testTokenizer()
    {
        $tokenizer = new PennTreeBankTokenizer();
        $this->assertCount(14, $tokenizer->tokenize("Good muffins cost $3.88\nin New York.  Please buy me\ntwo of them.\nThanks."));
    }
    
    public function testTokenizer2()
    {
        $tokenizer = new PennTreeBankTokenizer();
        $this->assertCount(6, $tokenizer->tokenize("They'll save and invest more."));
    }    
    
}

