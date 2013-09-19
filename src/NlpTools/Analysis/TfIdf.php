<?php
namespace NlpTools\Analysis;

use NlpTools\FeatureFactories\WeightedFeatures;


/**
 * IDF implementation used for determine the importance of a token in a document
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
     * Cache for saving IDF results
     * @var array 
     */
    protected $cachedIdf = array();
    
    /**
     * Cache for saving the document weghts into 
     * @var array 
     */
    protected $cachedWeights = array();
    
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
        foreach($documents as $documentName => $document){             
            $this->features = array_merge($weightedFeatures->getFeatureArray($documentName, $document), $this->features);
        }       
    }
    
    /**
     * For a given token compute the idf, the value is cached
     * @param string $token 
     * @return double
     */
    public function getIdf($token)
    {        
        if(!isset($this->cachedIdf[$token])){         
            $timesFound = 0;

            foreach($this->features as $featureSet){             
                if(isset($featureSet[$token])){ 
                    $timesFound++;
                }            
            }

            $timesFound = max($timesFound, 1);        
            $this->cachedIdf[$token] = log($this->totalDocuments / $timesFound);
        }    
        
        return $this->cachedIdf[$token];   
    }
    
    /**
     * Get an array containing the tf idf weights per document based on the provided token, a cache is used to store the values
     * @param string $token Token is a word to lookup
     * @return array Returns an array with the index as the document id and the value as the weight for that document
     */
    public function query($token)
    { 

        if(isset($this->cachedWeights[$token])){ 
            return $this->cachedWeights[$token];
        } else { 
            $this->cachedWeights[$token] = array();
        }
        //get the inverse document frequency
        $idf = $this->getIdf($token);
                
        $weights = array();
        
        foreach($this->features as $documentName => $tokens){             
            if(isset($tokens[$token])){ 
                $weights[$documentName] = $this->features[$documentName][$token] * $idf;
            } else {
                $weights[$documentName] = 0;
            } 
        }        
            
        $this->cachedWeights[$token] = array_merge($weights, $this->cachedWeights[$token]);
        return $this->cachedWeights[$token];
    }
}


