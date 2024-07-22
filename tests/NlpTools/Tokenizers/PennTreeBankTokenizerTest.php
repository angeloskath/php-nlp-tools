<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Dan Cardin
 */
class PennTreeBankTokenizerTest extends TestCase
{
    public function testTokenizer(): void
    {
        $pennTreeBankTokenizer = new PennTreeBankTokenizer();
        $tokens = $pennTreeBankTokenizer->tokenize("Good muffins cost $3.88\nin New York.  Please buy me\ntwo of them.\nThanks.");
        $this->assertCount(16, $tokens);
    }

    public function testTokenizer2(): void
    {
        $pennTreeBankTokenizer = new PennTreeBankTokenizer();
        $this->assertCount(7, $pennTreeBankTokenizer->tokenize("They'll save and invest more."));
    }

    public function testTokenizer3(): void
    {
        $pennTreeBankTokenizer = new PennTreeBankTokenizer();
        $this->assertCount(4, $pennTreeBankTokenizer->tokenize("I'm some text"));
    }

    public function testAgainstOriginalSedImplementation(): void
    {
        $pennTreeBankTokenizer = new PennTreeBankTokenizer();
        $tokenized = new \SplFileObject(TEST_DATA_DIR . "/Tokenizers/PennTreeBankTokenizerTest/tokenized");
        $tokenized->setFlags(\SplFileObject::DROP_NEW_LINE);

        $sentences = new \SplFileObject(TEST_DATA_DIR . "/Tokenizers/PennTreeBankTokenizerTest/test.txt");
        $sentences->setFlags(\SplFileObject::DROP_NEW_LINE);

        $tokenized->rewind();
        foreach ($sentences as $sentence) {
            if ($sentence) { // skip empty lines
                $this->assertEquals(
                    $tokenized->current(),
                    implode(" ", $pennTreeBankTokenizer->tokenize($sentence)),
                    sprintf("Sentence: '%s' was not tokenized correctly", $sentence)
                );
            }

            $tokenized->next();
        }
    }
}
