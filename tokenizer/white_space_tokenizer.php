<?php

namespace NlpTools;

class WhitespaceTokenizer implements Tokenizer
{
	public function tokenize($str) {
		$arr = array();
		preg_match_all('/(\s)*(\w+|.)(\s)*/',$str,$arr);
		return $arr[2];
	}
	
}

?>
