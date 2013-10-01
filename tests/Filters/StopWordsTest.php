<?php
namespace NlpTools\Filters;


/**
 * Stop word filter test
 * @author Dan Cardin (yooper)
 */
class StopWordsTest extends \PHPUnit_Framework_TestCase
{
    public function testIsStopWord()
    {
        $stopWord = new StopWords();
        $this->assertNull($stopWord->transform("again"));
    }
    
    public function testIsNotStopWord()
    {
        $stopWord = new StopWords();
        $this->assertEquals("Peninsula", $stopWord->transform("Peninsula"));
    }
}



