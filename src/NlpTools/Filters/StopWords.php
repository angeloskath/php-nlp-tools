<?php
namespace NlpTools\Filters;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;
use \InvalidArgumentException;

/**
 * Stop words is an english list of stop words
 * @author Dan Cardin (yooper)
 */
class StopWords implements TokenTransformationInterface
{    
    /**
     * An array of stop words
     * @var array 
     */
    protected $stopWords = null;
    
    /**
     * load in the stop words
     */
    public function __construct(array $stopWords)
    {
        if(!$this->isAssoc($stopWords)) { 
            throw new InvalidArgumentException("You must provide an associatve array");
        }
        
        $this->stopWords = $stopWords;
    }
    
    /**
     * Check if the array is an associative array
     * http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential
     * @param array $array
     * @return boolean 
     */
    protected function isAssoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
    
    /**
     * Checks if the token exists in the stop word lit
     * @param string $token 
     */
    public function transform($token)
    {
        return isset($this->stopWords[$token]) ? null : $token;
    }
     
    /**
     * init a list of stop words 
     */
    protected function initStopWords()
    {
        $stopWords =<<< STOPWORDS


STOPWORDS;
        
        //there is an extra new line that must be popped off
        $this->stopWords = explode(PHP_EOL, $stopWords);        
        array_pop($this->stopWords);
        
    } // end of init stop words
}

