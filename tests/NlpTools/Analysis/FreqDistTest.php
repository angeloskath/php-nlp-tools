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
        $document = new TokensDocument(array("time", "flies", "like", "an", "arrow", "time", "flies", "like", "what"));
        $freqDist = new FreqDist($document);
        $this->assertTrue(count($freqDist->getHapaxes()) === 3);        
        $this->assertEquals(9, $freqDist->getTotalTokens());
        $this->assertEquals(6, $freqDist->getTotalUniqueTokens());
    } 
}

