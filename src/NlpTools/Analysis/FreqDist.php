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
     * An associative array that holds all the frequencies per token
     * @var array
     */
    protected $keyValues = array();

    /**
     * The total number of tokens originally passed into FreqDist
     * @var int
     */
    protected $totalTokens = null;

    /**
     * This sorts the token meta data collection right away so use
     * frequency distribution data can be extracted.
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $this->preCompute($tokens);
        $this->totalTokens = count($tokens);
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
     * @param array $tokens The set of tokens passed into the constructor
     */
    protected function preCompute(array &$tokens)
    {
        //count all the tokens up and put them in a key value store
        $this->keyValues = array_count_values($tokens);
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
     * Return a token's count
     * @param string $string
     * @return mixed
     */
    public function getTotalByToken($string)
    {
        $array = $this->keyValues;
        if(array_key_exists($string, $array)) {
            return $array[$string];
        } else {
            return false;
        }
    }

    /**
     * Return a token's weight (for user's own tf-idf/pdf/iduf implem)
     * @param string $string
     * @return mixed
     */
    public function getTokenWeight($string)
    {
        if($this->getTotalByToken($string)){
            return $this->getTotalByToken($string)/$this->getTotalTokens();
        } else {
            return false;
        }
    }

    /**
     *
     * Returns an array of tokens that occurred once
     * @todo This is an inefficient approach
     * @return array
     */
    public function getHapaxes()
    {
        $samples = array();
        foreach ($this->getKeyValues() as $sample => $count) {
            if ($count == 1) {
                $samples[] = $sample;
            }
        }
        return $samples;
    }

}
