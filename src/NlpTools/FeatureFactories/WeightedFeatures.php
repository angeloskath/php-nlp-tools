<?php
namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;
use NlpTools\Analysis\FreqDist;

/**
 * Feature factory class that provides different weighting schemes
 * @author Dan Cardin (yooper)
 */
class WeightedFeatures implements FeatureFactoryInterface, \Countable
{
    /**
     * Default mode of weighting uses frequency 
     */
    const FREQUENCY_MODE = 1;
    const BOOLEAN_MODE = 2;
    const LOGARITHMIC_MODE = 3;
    const AUGMENTED_MODE = 4;
    
    /**
     *
     * @var int Default mode that uses frequencies 
     */
    protected $mode = 1;
    
    
    /**
     * @var int Total sum of tokens used in this document 
     */
    protected $totalTokens = 0;
    
    /**
     *
     * @param int $mode The type of mode to use for weighting
     */
    public function __construct($mode = self::FREQUENCY_MODE)
    {
        $this->mode = $mode;
    }
    
    /**
     * Return an associative array with keys ie tokens and the weights per document
     * Implementation is based off the wiki article http://en.wikipedia.org/wiki/Tf*idf
     * @param string $class
     * @param DocumentInterface $d
     * @return array 
     */
    public function getFeatureArray($class, DocumentInterface $d)
    {
        $freqDist = new FreqDist($d->getDocumentData());
        $keyValuesByWeight = $freqDist->getKeyValuesByFrequency();
        
        $this->totalTokens = array_sum($keyValuesByWeight);
        
        switch($this->mode) { 
                            
            case self::BOOLEAN_MODE:
                
                                                
                array_walk($keyValuesByWeight, function(&$value, $key) {
                    $value = true;
                });
                return $keyValuesByWeight;
                
            case self::LOGARITHMIC_MODE:
                                
                array_walk($keyValuesByWeight, function(&$value, $key) {
                    $value = log($value+1);
                });
                return $keyValuesByWeight;
            
            case self::AUGMENTED_MODE:
                
                $maxFrequency = max($keyValuesByWeight);
                
                array_walk($keyValuesByWeight, function(&$value, $key, $max) {
                    $value = 0.5 + (0.5 * $value) / $max;
                }, $maxFrequency);
                return $keyValuesByWeight;
                
            case self::FREQUENCY_MODE:
            default:
                return array($class => $freqDist->getKeyValuesByFrequency());
            
        }

    }

    /**
     * Count returns the total number of tokens that were originally passed in 
     * @return int Return the sum of all the tokens in the document
     */
    public function count()
    {
        return $this->totalTokens;
    }

    
}
