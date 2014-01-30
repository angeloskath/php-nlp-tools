<?php

namespace NlpTools\Stemmers;

use NlpTools\Documents\TokensDocument;

class TransformationTest extends \PHPUnit_Framework_TestCase
{
    public function provideStemmers()
    {
        return array(
            array(new LancasterStemmer()),
            array(new PorterStemmer())
        );
    }

    /**
     * @dataProvider provideStemmers
     */
    public function testStemmer(Stemmer $stemmer)
    {
        $tokens = explode(" ","this renowned monster who had come off victorious in a hundred fights with his pursuers was an old bull whale of prodigious size and strength from the effect of age or more probably from a freak of nature a singular consequence had resulted he was white as wool");
        $stemmed = $stemmer->stemAll($tokens);
        $doc = new TokensDocument($tokens);

        $this->assertNotEquals(
            $stemmed,
            $doc->getDocumentData()
        );

        $doc->applyTransformation($stemmer);
        $this->assertEquals(
            $stemmed,
            $doc->getDocumentData()
        );
    }
}
