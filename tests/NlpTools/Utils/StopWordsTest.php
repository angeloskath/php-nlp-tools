<?php

declare(strict_types=1);

namespace NlpTools\Utils;

use NlpTools\Documents\TokensDocument;
use NlpTools\Utils\Normalizers\Normalizer;
use PHPUnit\Framework\TestCase;

class StopWordsTest extends TestCase
{
    public function testStopwords(): void
    {
        $stopwords = new StopWords(
            ["to", "the"]
        );

        $tokensDocument = new TokensDocument(explode(" ", "if you tell the truth you do not have to remember anything"));
        $tokensDocument->applyTransformation($stopwords);
        $this->assertEquals(
            ["if", "you", "tell", "truth", "you", "do", "not", "have", "remember", "anything"],
            $tokensDocument->getDocumentData()
        );
    }

    public function testStopwordsWithTransformation(): void
    {
        $stopwords = new StopWords(
            ["to", "the"],
            Normalizer::factory("English")
        );

        $tokensDocument = new TokensDocument(explode(" ", "If you Tell The truth You do not have To remember Anything"));
        $tokensDocument->applyTransformation($stopwords);
        $this->assertEquals(
            ["If", "you", "Tell", "truth", "You", "do", "not", "have", "remember", "Anything"],
            $tokensDocument->getDocumentData()
        );
    }
}
