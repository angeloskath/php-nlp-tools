<?php

namespace NlpTools\Documents;

/*
 * Represents a bag of words (tokens) document.
 */
class TokensDocument implements Document
{
	protected $tokens;
	public function __construct(array $tokens) {
		$this->tokens = $tokens;
	}
	public function getDocumentData() {
		return $this->tokens;
	}
}

?>
