<?php

namespace NlpTools\Documents;

/**
 * Represents a bag of words (tokens) document.
 */
class TokensDocument implements Document
{
	protected $tokens;
	public function __construct(array $tokens) {
		$this->tokens = $tokens;
	}
	/**
	 * Simply return the tokens received in the constructor
	 * @return array The tokens array
	 */
	public function getDocumentData() {
		return $this->tokens;
	}
}

?>
