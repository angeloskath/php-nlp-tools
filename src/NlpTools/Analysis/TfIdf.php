<?php
namespace NlpTools\Analysis;

use NlpTools\FeatureFactories\WeightedFeatures;


/**
 * TF*IDF implementation used for determine the importance of a token in a document
 * @author Dan Cardin
 */
class TfIdf 
{
    /**
     * @var array The features for this set of documents 
     */
    protected $features = array();
        
    /**
     *
     * @var int The total number of documents in the collection 
     */
    protected $totalDocuments = 0;
    
    /**
     * A constructor for setting up the Tf IDF implementation
     * @param array $documents An array of documents
     * @param int $mode The mode to use in the weighted features
     */
    public function __construct(array $documents, $mode = WeightedFeatures::FREQUENCY_MODE)
    {        
                
        $weightedFeatures = new WeightedFeatures($mode);
        $this->totalDocuments = count($documents);
        
        //assume each document has a unique name
        foreach($documents as $document){             
            $this->features = array_merge($weightedFeatures->getFeatureArray($document->getName(), $document), $this->features);
        }       
    }
    
    /**
     * For a given token compute the idf
     * @param string $token 
     * @return double
     */
    protected function computedIdf($token)
    {        
        $timesFound = 0;
        
        foreach($this->features as $tokens){             
            if(array_key_exists($token, $tokens)){ 
                $timesFound++;
            }            
        }
        
        $timesFound = max($timesFound, 1);
        return log($this->totalDocuments / $timesFound);
        
    }
    
    /**
     * Get an array containing the tf idf weights per document based on the provided token
     * @param string $token Token is a word to lookup
     * @return array Returns an array with the index as the document id and the value as the weight for that document
     */
    public function query($token)
    { 
        //inverse document frequency
        $idf = $this->computedIdf($token);
        $weights = array();
        
        foreach($this->features as $documentName => $tokens){             
            if(array_key_exists($token, $tokens)){ 
                $weights[$documentName] = $this->features[$documentName][$token] * $idf;
            } else {
                $weights[$documentName] = 0;
            } 
        }        
            
        return $weights;
    }
    
    
    
    
}


