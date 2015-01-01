<?php
namespace NlpTools\Stemmers;
use NlpTools\Utils\VowelsAbstractFactory;
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
     * Used to check for vowels
     * @var VowelAbstractFactory
     */
    protected $vowelChecker = null;

    /**
     * Constructor loads the ruleset into memory
     * @param array $ruleSet the set of rules that will be used by the lancaster algorithm. if empty
     * this will use the default ruleset embedded in the LancasterStemmer
     */
    public function __construct($ruleSet = array())
    {
        //setup the default rule set
        if (empty($ruleSet)) {
            $ruleSet = LancasterStemmer::getDefaultRuleSet();
        }

        $this->indexRules($ruleSet);
        //only get the english vowel checker
        $this->vowelChecker = VowelsAbstractFactory::factory("English");
    }

    /**
     * Creates an chained hashtable using the lookup char as the key
     * @param array $rules
     */
    protected function indexRules(array $rules)
    {
        $this->indexedRules = array();

        foreach ($rules as $rule) {
            if (isset($this->indexedRules[$rule[self::LOOKUP_CHAR]])) {
                $this->indexedRules[$rule[self::LOOKUP_CHAR]][] = $rule;
            } else {
                $this->indexedRules[$rule[self::LOOKUP_CHAR]] = array($rule);
            }
        }
    }

    /**
     * Performs a Lancaster stem on the giving word
     * @param  string $word The word that gets stemmed
     * @return string The stemmed word
     */
    public function stem($word)
    {
        $this->originalToken = $word;

        // account for the case of the string being empty
        if (empty($word))
            return $word;

        //only iterate out loop if a rule is applied
        do {
            $ruleApplied = false;
            $lookupChar = $word[strlen($word)-1];

            //check that the last character is in the index, if not return the origin token
            if (!array_key_exists($lookupChar, $this->indexedRules)) {
                return $word;
            }

            foreach ($this->indexedRules[$lookupChar] as $rule) {
                if(strrpos($word, substr($rule[self::ENDING_STRING],-1)) ===
                        (strlen($word)-strlen($rule[self::ENDING_STRING]))){

                    if (!empty($rule[self::INTACT_FLAG])) {

                        if($this->originalToken == $word &&
                            $this->isAcceptable($word, (int) $rule[self::REMOVE_TOTAL])){

                            $word = $this->applyRule($word, $rule);
                            $ruleApplied = true;
                            if ($rule[self::CONTINUE_FLAG] === '.') {
                                return $word;
                            }
                            break;
                        }
                    } elseif ($this->isAcceptable($word, (int) $rule[self::REMOVE_TOTAL])) {
                        $word = $this->applyRule($word, $rule);
                        $ruleApplied = true;
                        if ($rule[self::CONTINUE_FLAG] === '.') {
                            return $word;
                        }
                        break;
                    }
                } else {
                    $ruleApplied = false;
                }
            }
        } while ($ruleApplied);

        return $word;

    }

    /**
     * Apply the lancaster rule and return the altered string.
     * @param string $word word the rule is being applied on
     * @param array  $rule An associative array containing all the data elements for applying to the word
     */
    protected function applyRule($word, $rule)
    {
        return substr_replace($word, $rule[self::APPEND_STRING], strlen($word) - $rule[self::REMOVE_TOTAL]);
    }

    /**
     * Check if a word is acceptable
     * @param  string  $word        The word under test
     * @param  int     $removeTotal The number of characters to remove from the suffix
     * @return boolean True is the word is acceptable
     */
    protected function isAcceptable($word, $removeTotal)
    {
        $length =  strlen($word) - $removeTotal;
        if ($this->vowelChecker->isVowel($word, 0)&& $length >= 2) {
            return true;
        } elseif($length >= 3 &&
                ($this->vowelChecker->isVowel($word, 1) || $this->vowelChecker->isVowel($word, 2))) {
            return true;
        }

        return false;
    }

    /**
     * Contains an array with the default lancaster rules
     * @return array
     */
    public static function getDefaultRuleSet()
    {
        return array(
            array(
                "lookup_char"=> "a",
                "ending_string"=> "ai",
                "intact_flag"=> "*",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "a",
                "ending_string"=> "a",
                "intact_flag"=> "*",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "b",
                "ending_string"=> "bb",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "c",
                "ending_string"=> "city",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "s",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "c",
                "ending_string"=> "ci",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "c",
                "ending_string"=> "cn",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "t",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "d",
                "ending_string"=> "dd",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "d",
                "ending_string"=> "dei",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "y",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "d",
                "ending_string"=> "deec",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "ss",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "d",
                "ending_string"=> "dee",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "d",
                "ending_string"=> "de",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "d",
                "ending_string"=> "dooh",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "e",
                "ending_string"=> "e",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "f",
                "ending_string"=> "feil",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "v",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "f",
                "ending_string"=> "fi",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "g",
                "ending_string"=> "gni",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "g",
                "ending_string"=> "gai",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "y",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "g",
                "ending_string"=> "ga",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "g",
                "ending_string"=> "gg",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "h",
                "ending_string"=> "ht",
                "intact_flag"=> "*",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "h",
                "ending_string"=> "hsiug",
                "intact_flag"=> "",
                "remove_total"=> "5",
                "append_string"=> "ct",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "h",
                "ending_string"=> "hsi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "i",
                "ending_string"=> "i",
                "intact_flag"=> "*",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "i",
                "ending_string"=> "i",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "y",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "ji",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "d",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "juf",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "s",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "ju",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "d",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "jo",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "d",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "jeh",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "r",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "jrev",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "t",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "jsim",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "t",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "jn",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "d",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "j",
                "ending_string"=> "j",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "s",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lbaifi",
                "intact_flag"=> "",
                "remove_total"=> "6",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lbai",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "y",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lba",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lbi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lib",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "l",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lc",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lufi",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "y",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "luf",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lu",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lai",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "lau",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "la",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "l",
                "ending_string"=> "ll",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "m",
                "ending_string"=> "mui",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "m",
                "ending_string"=> "mu",
                "intact_flag"=> "*",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "m",
                "ending_string"=> "msi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "m",
                "ending_string"=> "mm",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "nois",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "j",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "noix",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "ct",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "noi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "nai",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "na",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "nee",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "ne",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "n",
                "ending_string"=> "nn",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "p",
                "ending_string"=> "pihs",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "p",
                "ending_string"=> "pp",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "re",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "rae",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "ra",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "ro",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "ru",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "rr",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "rt",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "r",
                "ending_string"=> "rei",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "y",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "sei",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "y",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "sis",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "si",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "ssen",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "ss",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "suo",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "su",
                "intact_flag"=> "*",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "s",
                "intact_flag"=> "*",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "s",
                "ending_string"=> "s",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tacilp",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "y",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "ta",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tnem",
                "intact_flag"=> "",
                "remove_total"=> "4",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tne",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tna",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tpir",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "b",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tpro",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "b",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tcud",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tpmus",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tpec",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "iv",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tulo",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "v",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tsis",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tsi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "t",
                "ending_string"=> "tt",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "u",
                "ending_string"=> "uqi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "u",
                "ending_string"=> "ugo",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "v",
                "ending_string"=> "vis",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "j",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "v",
                "ending_string"=> "vie",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "v",
                "ending_string"=> "vi",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ylb",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yli",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "y",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ylp",
                "intact_flag"=> "",
                "remove_total"=> "0",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yl",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ygo",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yhp",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ymo",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ypo",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yti",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yte",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ytl",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yrtsi",
                "intact_flag"=> "",
                "remove_total"=> "5",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yra",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yro",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yfi",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> "."),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "ycn",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "t",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "y",
                "ending_string"=> "yca",
                "intact_flag"=> "",
                "remove_total"=> "3",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "z",
                "ending_string"=> "zi",
                "intact_flag"=> "",
                "remove_total"=> "2",
                "append_string"=> "",
                "continue_flag"=> ">"),
            array(
                "lookup_char"=> "z",
                "ending_string"=> "zy",
                "intact_flag"=> "",
                "remove_total"=> "1",
                "append_string"=> "s",
                "continue_flag"=> ".")
        );
    }

}
