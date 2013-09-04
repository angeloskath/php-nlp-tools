<?php
namespace NlpTools\Analysis;
use NlpTools\Documents\TokensDocument;


/**
 * Test the FreqDist class
 *
 * @author Dan Cardin
 */
class FreqDistTest extends \PHPUnit_Framework_TestCase
{   
    public function testSimpleFreqDist()
    { 
        $freqDist = new FreqDist(array("time", "flies", "like", "an", "arrow", "time", "flies", "like", "what"));
        $this->assertTrue(count($freqDist->getHapaxes()) === 3);        
        $this->assertEquals(9, $freqDist->getTotalTokens());
        $this->assertEquals(6, $freqDist->getTotalUniqueTokens());
    } 
}

