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
    
    public function testComparison()
    {
        $this->markTestIncomplete(
          'This test is not complete, because it does not work to compare the outputs, because the underlying tokenizers differ in behavior.'
        );
        //test data is based on the same test file, the tokens are unique and lower case
        $testWordSet = explode(PHP_EOL, file_get_contents(TEST_DATA_DIR.'/Tokenizers/PennTreeBankTokenizerTest/nltk_output.txt'));
            
        $tokenizer = new PennTreeBankTokenizer();
        //text is normalized to lower case
        $tokens = $tokenizer->tokenize(strtolower(file_get_contents(TEST_DATA_DIR.'/Tokenizers/PennTreeBankTokenizerTest/test.txt')));
        $tokens = array_unique($tokens);
        sort($tokens);
        
        foreach($testWordSet as $testWord){ 
            $this->assertTrue(in_array($testWord, $tokens), "The tokenized word '$testWord' was not found.");
        }
                
    }

}
