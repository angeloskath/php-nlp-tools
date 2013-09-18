<?php
namespace NlpTools\Analysis;
use NlpTools\Documents\TokensDocument;


/**
 * Test the TFIDF class
 *
 * @author Dan Cardin
 */
class TfIdfTest extends \PHPUnit_Framework_TestCase
{   
    public function testSimpleTfIdf()
    { 
        $documents = array(
            new TokensDocument(array("marquette", "michigan", "hiking", "hiking", "hiking" , "camping", "swimming"), "doc1"), 
            new TokensDocument(array("ironwood", "michigan", "hiking", "biking", "camping", "swimming","marquette"), "doc2"),
            new TokensDocument(array("no","tokens"), "doc3")
        );
        
        $tfidf = new TfIdf($documents);
        $results = $tfidf->query("hiking");
        
        $this->assertTrue($results['doc1'] > 1.21);
        $this->assertTrue($results['doc2'] > 0.4);
        $this->assertTrue($results['doc3'] == 0);                        
        
    }
    
}