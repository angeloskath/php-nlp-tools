<?php
namespace NlpTools\Analysis;

use NlpTools\Documents\TokensDocument;

/**
 * Extract the Frequency distribution of keywords
 * @author Dan Cardin
 */
class FreqDist 
{
    /**
     * The TokensDocument passed in
     * @var TokensDocument
     */
    protected $tokensDocument = null;
    
    
    /**
     * An associative array that holds all the frequencies per token
     * @var array 
     */
    protected $keyValues = array();
    
    /**
     * This sorts the token meta data collection right away so use 
     * frequency distribution data can be extracted.
     * @param TokensDocument
     */
    public function __construct(TokensDocument &$document)
    {
        $this->tokensDocument  = $document;   
        $this->preCompute();
    }
     
    /**
     * Get the total number of tokens in this tokensDocument
     * @return int 
     */
    public function getTotalTokens()
    {
        return count($this->tokensDocument->getDocumentData());
    }
    
    /**
     * Internal function for summarizing all the data into a key value store
     */
    public function preCompute()
    {
        //count all the tokens up and put them in a key value store
        $this->keyValues = array_count_values($this->tokensDocument->getDocumentData());
        arsort($this->keyValues);        
    } 
    

    /**
     * Return the weight of a single token
     * @return float 
     */
    public function getWeightPerToken()
    {
        return 1 / $this->getTotalTokens();
    }
    
    /**
     * Return get the total number of unique tokens
     * @return int
     */
    public function getTotalUniqueTokens()
    {
        return count($this->keyValues);
    }
    
    /**
     * Return the sorted keys by frequency desc
     * @return array 
     */
    public function getKeys()
    {
        return array_keys($this->keyValues);
    }
    
    /**
     * Return the sorted values by frequency desc
     * @return array 
     */
    public function getValues()
    {
        return array_values($this->keyValues);
    }
    
    /**
     * Return the full key value store
     * @return array 
     */
    public function getKeyValues()
    {
        return $this->keyValues;
    }
    
    /**
     * 
     * Returns an array of tokens that occurred once 
     * @todo This is an inefficient approach
     * @return array
     */
    public function getHapaxes()
    {
            $hapaxes = array();
            //resort the array
            $reversed = array_reverse($this->keyValues);
            
            foreach($reversed as $key => $freq){
                if($freq === 1){
                    $hapaxes[] = $key;
                }
                elseif($freq > 1){ 
                    break;
                }
            }
            
            unset($reversed);
            return $hapaxes; 
    }
    
}

