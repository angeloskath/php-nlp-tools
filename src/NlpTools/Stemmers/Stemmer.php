<?php

namespace NlpTools\Stemmers;

use NlpTools\Utils\TransformationInterface;

/**
 * http://en.wikipedia.org/wiki/Stemming
 */
abstract class Stemmer implements TransformationInterface
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
     * A stemmer's transformation is simply the replacing of a word
     * with its stem.
     */
    public function transform($word)
    {
        return $this->stem($word);
    }
}
