<?php

namespace NlpTools\Documents;

/**
 * A Document that represents a single word but with a context of a
 * larger document. Useful for Named Entity Recognition
 */
class WordDocument implements Document
{
	protected $word;
	protected $before;
	protected $after;
	public function __construct(array $tokens, $index, $context) {
		$this->word = $tokens[$index];
		
		$this->before = array();
		for ($start = max($index-$context,0);$start<$index;$start++)
		{
			$this->before[] = $tokens[$start];
		}
		
		$this->after = array();
		$end = min($index+$context,count($tokens));
		for ($start = $index+1;$start<$end;$start++)
		{
			$this->after[] = $tokens[$start];
		}
	}
	
	/**
	 * It returns an array with the first element being the actual word,
	 * the second element being an array of previous words, and the
	 * third an array of following words
	 * 
	 * @return array
	 */
	public function getDocumentData() {
		return array($this->word,$this->before,$this->after);
	}
}

?>
