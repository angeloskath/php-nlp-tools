<?php
namespace NlpTools\Analysis;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\TrainingDocument;

/**
 * Test the Idf class
 *
 * @author Dan Cardin
 */
class IdfTest extends \PHPUnit_Framework_TestCase
{   
    public function testSimpleTfIdf()
    { 
        
        $ts = new TrainingSet();
        $ts->addDocument("doc1", new TokensDocument(array("marquette", "michigan", "hiking", "hiking", "hiking" , "camping", "swimming")));
        $ts->addDocument("doc2", new TokensDocument(array("ironwood", "michigan", "hiking", "biking", "camping", "swimming","marquette")));
        $ts->addDocument("doc3", new TokensDocument(array("no","tokens")));

           
        $idf = new Idf($ts);
        $results = $idf->query("hiking");
        $this->assertTrue($results['doc1'] > 1.21);
        $this->assertTrue($results['doc2'] > 0.4);
        $this->assertTrue($results['doc3'] == 0);                        
        
    }
    
}