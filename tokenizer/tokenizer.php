<?php

namespace NlpTools;

interface Tokenizer
{
	/*
	 * @param string $str The text for tokenization
	 * @return array The list of tokens from the string
	 */
	public function tokenize($str);
}

?>
