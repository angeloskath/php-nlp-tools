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
     * The total number of tokens and times they occur
     * @var int 
     */
    protected $totalTokens = 0;
    
    /**
     * An associative array that holds all the weights per token
     * @var array 
     */
    protected $keyValues = array();
    
    /**
     * This sorts the token meta data collection right away so use 
     * frequency distribution data can be extracted.
     * @param TokensDocument
     */
    public function __construct(TokensDocument $document)
    {
        $this->tokensDocument  = $document;   
        $this->totalTokens = count($this->tokensDocument->getDocumentData());
        $this->preCompute();
    }
     
    /**
     * Get the total number of tokens in this tokensDocument
     * @return int 
     */
    public function getTotalTokens()
    {
        return $this->totalTokens;
    }
    
    /**
     * Internal function for summarizing all the data into a key value store
     */
    public function preCompute()
    {
        //count all the tokens up and put them in a key value store
        foreach($this->tokensDocument->getDocumentData() as $token){
            if(!array_key_exists($token, $this->keyValues)){ 
                $this->keyValues[$token] = 0;
            }
            
            $this->keyValues[$token]++;
        }                            
        arsort($this->keyValues);        
    } 
    

    /**
     * Return the weight of a single token
     * @return float 
     */
    public function getWeightPerToken()
    {
        return 1 / $this->totalTokens;
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
            asort($this->keyValues);
            
            foreach($this->keyValues as $key => $freq){
                if($freq === 1){
                    $hapaxes[] = $key;
                }
                elseif($freq > 1){ 
                    break;
                }
            }
            
            //resort the key value store into the right order
            arsort($this->keyValues);
            return $hapaxes; 
    }
    
}

