<?php

namespace NlpTools;

/*
 * Simple white space tokenizer. Breaks either on whitespace or on word
 * boundaries (ex.: dots, commas, etc)
 * Does not include white space in tokens.
 * Every punctuation character is a signle token
 */
class WhitespaceAndPunctuationTokenizer implements Tokenizer
{
	public function tokenize($str) {
		$arr = array();
		preg_match_all('/(\s*)([^\pP\s]+|.)(\s*)/u',$str,$arr);
		return $arr[2];
	}
}

?>
