<?php
namespace NlpTools\Utils\StopWords;

/**
 *
 * @author Dan Cardin
 */
class EnglishStopWordsTest extends \PHPUnit_Framework_TestCase
{
    public function testStopWordSize()
    {       
        $stopWordsSet = StopWordsAbstractFactory::factory();
        $this->assertCount(595, $stopWordsSet->getStopWords());
    }
    

}
