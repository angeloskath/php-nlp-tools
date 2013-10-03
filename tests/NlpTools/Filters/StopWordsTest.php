<?php
namespace NlpTools\Filters;


/**
 * Stop word filter test
 * @author Dan Cardin (yooper)
 */
class StopWordsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Return an associatve array of the stop words
     * @staticvar array $cachedStopWords
     * @return array 
     */
    protected function getStopWords()
    {
        static $cachedStopWords = null;
        if(!$cachedStopWords) {
            $stopWords = explode(PHP_EOL,file_get_contents(TEST_DATA_DIR.'/Filters/StopWordTest/stop_words.txt'));
            $cachedStopWords = array_combine($stopWords, $stopWords);
        }
        
        return $cachedStopWords;
    }
    
    public function testIsStopWord()
    {
        $stopWord = new StopWords($this->getStopWords());
        $this->assertNull($stopWord->transform("again"));
    }
    
    public function testIsNotStopWord()
    {
        $stopWord = new StopWords($this->getStopWords());
        $this->assertEquals("Peninsula", $stopWord->transform("Peninsula"));
    }
}



