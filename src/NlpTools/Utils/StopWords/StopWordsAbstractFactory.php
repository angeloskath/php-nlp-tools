<?php
namespace NlpTools\Utils\StopWords;

/**
 * Factory wrapper for Stop words to support multiple stop word sets
 * @author Dan Cardin
 */
abstract class StopWordsAbstractFactory 
{
    /**
     * An array of strings containing all the stop words
     * @var type 
     */
    protected $stopWords = array();
    
    /**
     * Protected from public use, initializes the stop word set 
     * supplied internally by the inherited class 
     */
    protected function __construct()
    {
        $this->initStopWords();
    }
   
    /**
     * Return the correct stop words set.
     * @param string $language
     * @return \NlpTools\Utils\StopWords\StopWordsAbstractFactory
     * @throws \Exception 
     */
    static public function factory($stopWordsSet = 'English')
    {
        $className = "\\".__NAMESPACE__."\\{$stopWordsSet}StopWords";
        if(class_exists($className)) { 
            return new $className();
        }         
        throw new \Exception("Class $className does not exist");
    }

    /**
     * Get the array of stop words
     * @return array of strings  
     */
    public function getStopWords()
    {
        return $this->stopWords;
    }
    
    /**
     * Each sub class must implement the initial set of stop words; 
     */
    abstract public function initStopWords();

    /**
     * Add stop words to the list
     * @param string $word The stop word
     */
    public function addStopWord($word)
    {
        $this->stopWords[] = $word;
    }
            
}