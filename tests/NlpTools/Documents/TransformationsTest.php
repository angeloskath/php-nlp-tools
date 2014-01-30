<?php

namespace NlpTools\Documents;

use NlpTools\Utils\IdentityTransformer;

class TransformationsTest extends \PHPUnit_Framework_TestCase
{
    public function provideTokens()
    {
        return array(
            array(array("1","2","3","4","5","6","7"))
        );
    }

    /**
     * @dataProvider provideTokens
     */
    public function testTokensDocument($tokens)
    {
        $doc = new TokensDocument($tokens);
        $transformer = new IdentityTransformer();
        $this->assertEquals(
            $tokens,
            $doc->getDocumentData()
        );
        $doc->applyTransformation($transformer);
        $this->assertEquals(
            $tokens,
            $doc->getDocumentData()
        );

        $tdoc = new TrainingDocument("", new TokensDocument($tokens));
        $tdoc->applyTransformation($transformer);
        $this->assertEquals(
            $tokens,
            $tdoc->getDocumentData()
        );
    }

    /**
     * @dataProvider provideTokens
     */
    public function testWordDocument($tokens)
    {
        $transformer = new IdentityTransformer();
        $doc = new WordDocument($tokens,count($tokens)/2, 2);
        $correct = $doc->getDocumentData();
        $doc->applyTransformation($transformer);
        $this->assertEquals(
            $correct,
            $doc->getDocumentData()
        );

        $tdoc = new TrainingDocument("", new WordDocument($tokens,count($tokens)/2, 2));
        $tdoc->applyTransformation($transformer);
        $this->assertEquals(
            $correct,
            $tdoc->getDocumentData()
        );
    }
}
