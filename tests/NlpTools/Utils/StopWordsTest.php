<?php

namespace NlpTools\Utils;

use NlpTools\Documents\TokensDocument;

class StopWordsTest extends \PHPUnit_Framework_TestCase
{
    public function testStopwords()
    {
        $stopwords = new StopWords(
            array(
                "to",
                "the"
            )
        );

        $doc = new TokensDocument(explode(" ","if you tell the truth you do not have to remember anything"));
        $doc->applyTransformation($stopwords);
        $this->assertEquals(
            array(
                "if", "you", "tell", "truth", "you", "do", "not", "have", "remember", "anything"
            ),
            $doc->getDocumentData()
        );
    }
}
