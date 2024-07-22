<?php

declare(strict_types=1);

namespace NlpTools\Stemmers;

use NlpTools\Stemmers\GreekStemmer;
use PHPUnit\Framework\TestCase;

class GreekStemmerTest extends StemmerTestBase
{
    /**
     * Test the words found in Appendix A from Mr. Ntais's thesis.
     *
     * The words are not tested against the stem reported in the appendix
     * but against the results of Mr. Ntais's canonical implementation in js
     * found here http://people.dsv.su.se/~hercules/greek_stemmer.gr.html
     */
    public function testFromAppendixA(): void
    {
        $words = new \SplFileObject(TEST_DATA_DIR . '/Stemmers/GreekStemmerTest/appendix-a-words');
        $stems = new \SplFileObject(TEST_DATA_DIR . '/Stemmers/GreekStemmerTest/appendix-a-stems');
        $words->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);
        $stems->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);
        $stems->rewind();

        $greekStemmer = new GreekStemmer();
        $this->checkStemmer($greekStemmer, $words, $stems);
    }
}
