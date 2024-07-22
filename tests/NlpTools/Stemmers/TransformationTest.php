<?php

declare(strict_types=1);

namespace NlpTools\Stemmers;

use NlpTools\Documents\TokensDocument;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TransformationTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    public static function provideStemmers(): array
    {
        return [
            'LancasterStemmer' => [new LancasterStemmer()],
            'PorterStemmer' => [new PorterStemmer()]
        ];
    }

    #[DataProvider('provideStemmers')]
    public function testStemmer(Stemmer $stemmer): void
    {
        $tokens = explode(" ", "this renowned monster who had come off victorious in a hundred fights with his pursuers was an old bull whale of prodigious size and strength from the effect of age or more probably from a freak of nature a singular consequence had resulted he was white as wool");
        $stemmed = $stemmer->stemAll($tokens);
        $tokensDocument = new TokensDocument($tokens);

        $this->assertNotEquals(
            $stemmed,
            $tokensDocument->getDocumentData()
        );

        $tokensDocument->applyTransformation($stemmer);
        $this->assertEquals(
            $stemmed,
            $tokensDocument->getDocumentData()
        );
    }
}
