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
    public function testStopWordTransformation()
    {
        $ts = new TrainingSet();
        $ts->addDocument('test', new TokensDocument(array('the','cat','and', 'the', 'hat')));
        $ts->addTransformation(new StopWords());
        $ts->applyTransformations();
        
        $tokens = $ts->offsetGet(0)->getDocumentData();
        $this->assertEquals(array(null,'cat',null,null,'hat'), $tokens);
    }
    
    public function testStemmerAndStopWordTransformation()
    {
        $ts = new TrainingSet();
        $ts->addDocument('test', new TokensDocument(array('the','cat','and', 'the', 'hat','testing','rational','national')));
        $ts->addTransformation(new PorterStemmer());
        $ts->addTransformation(new StopWords());
        $ts->applyTransformations();
        
        $tokens = $ts->offsetGet(0)->getDocumentData();
        $this->assertEquals(array(null,'cat',null,null,'hat','test','ration','nation'), $tokens);
    }    
    
    
}
