<?php

namespace NlpTools;

/*
 * This stemmer removes affixes according to a regular expression.
 * */
class RegexStemmer implements Stemmer
{
	
	protected $regex;
	protected $min;
	
	public function __construct($regexstr,$min=0) {
		$this->regex = $regexstr;
		$this->min = $min;
	}
	
	public function stem($word) {
		if (mb_strlen($word,'utf-8')>=$this->min)
			return preg_replace($this->regex,'',$word);
		return $word;
	}
	
}

?>
