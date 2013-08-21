<?php

namespace NlpTools\Stemmers;

/**
 * This stemmer removes affixes according to a regular expression.
 */
class RegexStemmer extends Stemmer
{

    protected $regex;
    protected $min;

    /**
     * @param string  $regexstr The regex that will be passed to preg_replace
     * @param integer $min      Do nothing for tokens smaller than $min length
     */
    public function __construct($regexstr,$min=0)
    {
        $this->regex = $regexstr;
        $this->min = $min;
    }

    public function stem($word)
    {
        if (mb_strlen($word,'utf-8')>=$this->min)
            return preg_replace($this->regex,'',$word);
        return $word;
    }

}
