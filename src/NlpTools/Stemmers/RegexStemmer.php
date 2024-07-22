<?php

declare(strict_types=1);

namespace NlpTools\Stemmers;

/**
 * This stemmer removes affixes according to a regular expression.
 */
class RegexStemmer extends Stemmer
{
    /**
     * @param string $regex The regex that will be passed to preg_replace
     * @param integer $min      Do nothing for tokens smaller than $min length
     */
    public function __construct(protected string $regex, protected int $min = 0)
    {
    }

    public function stem(string $word): string
    {
        if (mb_strlen($word, 'utf-8') >= $this->min) {
            return preg_replace($this->regex, '', $word);
        }

        return $word;
    }
}
