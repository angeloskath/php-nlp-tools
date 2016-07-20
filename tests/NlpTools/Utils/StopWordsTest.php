<?php

namespace NlpTools\Utils;

use NlpTools\Documents\TokensDocument;
use NlpTools\Utils\Normalizers\Normalizer;

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

    public function testStopwordsWithTransformation()
    {
        $stopwords = new StopWords(
            array(
                "to",
                "the"
            ),
            Normalizer::factory("English")
        );

        $doc = new TokensDocument(explode(" ", "If you Tell The truth You do not have To remember Anything"));
        $doc->applyTransformation($stopwords);
        $this->assertEquals(
            array(
                "If", "you", "Tell", "truth", "You", "do", "not", "have", "remember", "Anything"
            ),
            $doc->getDocumentData()
        );
    }
}
