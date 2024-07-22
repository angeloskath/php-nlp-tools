<?php

declare(strict_types=1);

namespace NlpTools\Stemmers;

use PHPUnit\Framework\TestCase;

/**
 * Description of LancasterStemmerTest
 *
 * @author Dan Cardin
 */
class LancasterStemmerTest extends TestCase
{
    public function testLancasterStemmper(): void
    {
        $lancasterStemmer = new LancasterStemmer();
        $this->assertEquals('maxim', $lancasterStemmer->stem('maximum'));
        $this->assertEquals('presum', $lancasterStemmer->stem('presumably'));
        $this->assertEquals('multiply', $lancasterStemmer->stem('multiply'));
        $this->assertEquals('provid', $lancasterStemmer->stem('provision'));
        $this->assertEquals('ow', $lancasterStemmer->stem('owed'));
        $this->assertEquals('ear', $lancasterStemmer->stem('ear'));
        $this->assertEquals('say', $lancasterStemmer->stem('saying'));
        $this->assertEquals('cry', $lancasterStemmer->stem('crying'));
        $this->assertEquals('string', $lancasterStemmer->stem('string'));
        $this->assertEquals('meant', $lancasterStemmer->stem('meant'));
        $this->assertEquals('cem', $lancasterStemmer->stem('cement'));
    }

    /**
     * Added to cover issue #34
     */
    public function testEmptyStringForWord(): void
    {
        $lancasterStemmer = new LancasterStemmer();
        $this->assertEquals("", $lancasterStemmer->stem(""));
    }
}
