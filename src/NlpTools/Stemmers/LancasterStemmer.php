<?php
namespace NlpTools\Stemmers;
use NlpTools\Interfaces\IDataReader;
use NlpTools\Adapters\JsonDataReaderAdapter;
use NlpTools\Utils\Vowel;
/**
 * A word stemmer based on the Lancaster stemming algorithm.
 * Paice, Chris D. "Another Stemmer." ACM SIGIR Forum 24.3 (1990): 56-61.
 *
 * @author Dan Cardin
 */
class LancasterStemmer extends Stemmer
{
    /**
     * Constants used to make accessing the indexed array easier 
     */
    const ENDING_STRING = 'ending_string';
    const LOOKUP_CHAR = 'lookup_char';
    const INTACT_FLAG = 'intact_flag';
    const REMOVE_TOTAL = 'remove_total';
    const APPEND_STRING = 'append_string';
    const CONTINUE_FLAG = 'continue_flag';
    
    /**
    * Keep a copy of the original token
    * @var string 
    */
    protected $originalToken = null;
        
    /**
     * The indexed rule set provided
     * @var array
     */
    protected $indexedRules = array();
    
    /**
     * if reader is null it loads the default lancaster json rule set  
     * @param $reader IDataReader Generic data reader 
     */
    public function __construct(IDataReader $reader = null)
    {
        if(!$reader) { 
            $reader = new JsonDataReaderAdapter(file_get_contents(dirname(__DIR__).'/Files/Stemmers/lancaster.json'));
        }

        $this->indexRules($reader->read());        
    }
    
    /**
     * Creates an chained hashtable using the lookup char as the key
     * @param array $rules 
     */
    protected function indexRules($rules)
    {
        $this->indexedRules = array();
        
        foreach($rules as $rule){
            if(isset($this->indexedRules[$rule[self::LOOKUP_CHAR]])){
                $this->indexedRules[$rule[self::LOOKUP_CHAR]][] = $rule;
            } else {
                $this->indexedRules[$rule[self::LOOKUP_CHAR]] = array($rule);
            }
        }       
    }
    
    /**
     * Performs a Lancaster stem on the giving word
     * @param string $word The word that gets stemmed
     * @return string The stemmed word
     */
    public function stem($word)
    {
        $this->originalToken = $word;
        
        //only iterate out loop if a rule is applied        
        do {
            $ruleApplied = false;
            $lookupChar = $word[strlen($word)-1];

            //check that the last character is in the index, if not return the origin token
            if(!array_key_exists($lookupChar, $this->indexedRules)){
                return $word;
            }
            foreach($this->indexedRules[$lookupChar] as $rule)
            {
                if(strrpos($word, substr($rule[self::ENDING_STRING],-1)) === 
                        (strlen($word)-strlen($rule[self::ENDING_STRING]))){

                    
                    if(!empty($rule[self::INTACT_FLAG])){ 
                        
                        if($this->originalToken == $word && 
                            $this->isAcceptable($word, (int)$rule[self::REMOVE_TOTAL])){

                            $word = $this->applyRule($word, $rule);
                            $ruleApplied = true;
                            if($rule[self::CONTINUE_FLAG] === '.'){
                                return $word;
                            } 
                            break;
                        }
                    } elseif($this->isAcceptable($word, (int)$rule[self::REMOVE_TOTAL])){
                        $word = $this->applyRule($word, $rule);
                        $ruleApplied = true;
                        if($rule[self::CONTINUE_FLAG] === '.'){
                            return $word;
                        }
                        break;
                    }
                } else {
                    $ruleApplied = false;
                }
            }
        } while($ruleApplied);
        
        return $word;
                        
    }
    
    /**
     * Apply the lancaster rule and return the altered string. 
     * @param string $word word the rule is being applied on
     * @param array $rule An associative array containing all the data elements for applying to the word
     */
    protected function applyRule($word, $rule)
    {
        return substr_replace($word, $rule[self::APPEND_STRING], strlen($word) - $rule[self::REMOVE_TOTAL]);        
    }
    
    /**
     * Check if a word is acceptable
     * @param string $word The word under test
     * @param int $removeTotal The number of characters to remove from the suffix
     * @return boolean True is the word is acceptable
     */
    protected function isAcceptable($word, $removeTotal)
    {
        $length =  strlen($word) - $removeTotal;
        if(Vowel::isVowel($word, 0)&& $length >= 2){
            return true;
        } elseif($length >= 3 && 
                (Vowel::isVowel($word, 1) || Vowel::isVowel($word, 2))) {
            return true;
        }
        return false;
    }
        
}

