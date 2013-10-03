<?php
namespace NlpTools\Documents;
use NlpTools\Filters\StopWords;
use NlpTools\Stemmers\PorterStemmer;

/**
 * Test the training set
 * @author Dan Cardin (yooper)
 */
class TrainingSetTest extends \PHPUnit_Framework_TestCase
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
    
    public function testStopWordTransformation()
    {
        $ts = new TrainingSet();
        
        $ts->addDocument('test', new TokensDocument(array('the','cat','and', 'the', 'hat')));
        $ts->applyTransformations(array(new StopWords($this->getStopWords())));
        
        $tokens = $ts[0]->getDocumentData();
        $this->assertEquals(array('cat','hat'), $tokens);
    }
    
    public function testStemmerAndStopWordTransformation()
    {
        $ts = new TrainingSet();
        $ts->addDocument('test', new TokensDocument(array('the','cat','and', 'the', 'hat','testing','rational','national')));
        $ts->applyTransformations(array(new PorterStemmer(), new StopWords($this->getStopWords())));
        
        $tokens = $ts[0]->getDocumentData();
        $this->assertEquals(array('cat','hat','test','ration','nation'), $tokens);
    }    
    
    
}
