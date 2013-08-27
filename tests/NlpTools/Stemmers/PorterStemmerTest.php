<?php

namespace NlpTools\Stemmers;

/**
 * Check the correctness of the porter stemmer implementation
 *
 * words.txt and stems.txt are taken from
 * http://tartarus.org/~martin/PorterStemmer/
 */
class PorterStemmerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Load a set of words and their stems and check if the stemmer
     * produces the correct stems
     *
     * @group Slow
     */
    public function testStemmer()
    {
        $words = new \SplFileObject(TEST_DATA_DIR.'/Stemmers/PorterStemmerTest/words.txt');
        $stems = new \SplFileObject(TEST_DATA_DIR.'/Stemmers/PorterStemmerTest/stems.txt');
        $words->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);
        $stems->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);
        $stems->rewind();

        $stemmer = new PorterStemmer();
        foreach ($words as $word) {
            $stem = $stems->current();
            $this->assertEquals(
                $stemmer->stem($word),
                $stem,
                "The stem for '$word' should be '$stem' not '{$stemmer->stem($word)}'"
            );
            $stems->next();
        }
    }
}
