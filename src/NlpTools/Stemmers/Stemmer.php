<?php

namespace NlpTools\Stemmers;

/**
 * http://en.wikipedia.org/wiki/Stemming
 */
abstract class Stemmer
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
	public function stemAll(array $tokens) {
		return array_map(array($this,'stem'),$tokens);
	}
	
}

?>
