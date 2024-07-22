<?php

declare(strict_types=1);

namespace NlpTools\Analysis;

use NlpTools\Documents\TokensDocument;
use PHPUnit\Framework\TestCase;

/**
 * Test the FreqDist class
 *
 * @author Dan Cardin
 */
class FreqDistTest extends TestCase
{
    public function testSimpleFreqDist(): void
    {
        $freqDist = new FreqDist(["time", "flies", "like", "an", "arrow", "time", "flies", "like", "what"]);
        $this->assertTrue(count($freqDist->getHapaxes()) === 3);
        $this->assertEquals(9, $freqDist->getTotalTokens());
        $this->assertEquals(6, $freqDist->getTotalUniqueTokens());
    }

    public function testSimpleFreqWeight(): void
    {
        $freqDist = new FreqDist(["time", "flies", "like", "an", "arrow", "time", "flies", "like", "what"]);
        $this->assertEquals(1, $freqDist->getTotalByToken('an'));
        $this->assertEquals(0.111, round($freqDist->getTokenWeight('an'), 3));
    }

    public function testEmptyHapaxesFreqDist(): void
    {
        $freqDist = new FreqDist(["time", "time", "what", "what"]);
        $this->assertTrue($freqDist->getHapaxes() === []);
        $this->assertEquals(4, $freqDist->getTotalTokens());
        $this->assertEquals(2, $freqDist->getTotalUniqueTokens());
    }

    public function testSingleHapaxFreqDist(): void
    {
        $freqDist = new FreqDist(["time"]);
        $this->assertTrue(count($freqDist->getHapaxes()) === 1);
        $this->assertEquals(1, $freqDist->getTotalTokens());
        $this->assertEquals(1, $freqDist->getTotalUniqueTokens());
    }
}
