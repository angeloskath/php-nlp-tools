<?php

namespace NlpTools\Stemmers;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;

/**
 * http://en.wikipedia.org/wiki/Stemming
 */
abstract class Stemmer implements TokenTransformationInterface
{

    /**
     * Remove the suffix from $word
     *
     * @return string
     */
    abstract public function stem($word);

    /**
     * Apply the stemmer to every single token.
     *
     * @return array
     */
    public function stemAll(array $tokens)
    {
        return array_map(array($this,'stem'),$tokens);
    }
    
    /**
     * Wrap the stem method so the transform interface works
     * @param string $token
     * @return string 
     */
    public function transform($token) 
    { 
        return $this->stem($token);
    }

}
