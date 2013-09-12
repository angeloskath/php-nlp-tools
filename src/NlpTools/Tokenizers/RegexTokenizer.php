<?php

namespace NlpTools\Tokenizers;

/**
 * Regex tokenizer tokenizes text based on a set of regexes
 */
class RegexTokenizer implements TokenizerInterface
{
    // the patterns to be used
    protected $patterns;

    /**
     * Initialize the Tokenizer
     *
     * @param array $patterns The regular expressions
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }

    /**
     * Iteratively run for each pattern. The tokens resulting from one pattern are
     * fed to the next as strings.
     *
     * If the pattern is given alone, it is assumed that it is a pattern used
     * for splitting with preg_split.
     *
     * If the pattern is given together with an integer then it is assumed to be
     * a pattern used with preg_match
     *
     * If a pattern is given with a string it is assumed to be a transformation
     * pattern used with preg_replace
     *
     * @param  string $str The string to be tokenized
     * @return array  The tokens
     */
    public function tokenize($str)
    {
        $str = array($str);
        foreach ($this->patterns as $p) {
            if (!is_array($p)) $p = array($p);
            if (count($p)==1) { // split pattern
                $this->split($str, $p[0]);
            } elseif (is_int($p[1])) { // match pattern
                $this->match($str, $p[0], $p[1]);
            } else { // replace pattern
                $this->replace($str, $p[0], $p[1]);
            }
        }

        return $str;
    }

    /**
     * Execute the SPLIT mode
     *
     * @param array &$str The tokens to be further tokenized
     */
    protected function split(array &$str, $pattern)
    {
        $tokens = array();
        foreach ($str as $s) {
            $tokens = array_merge(
                $tokens,
                preg_split($pattern, $s, null, PREG_SPLIT_NO_EMPTY)
            );
        }

        $str = $tokens;
    }

    /**
     * Execute the KEEP_MATCHES mode
     *
     * @param array &$str The tokens to be further tokenized
     */
    protected function match(array &$str, $pattern, $keep)
    {
        $tokens = array();
        foreach ($str as $s) {
            preg_match_all($pattern, $s, $m);
            $tokens = array_merge(
                $tokens,
                $m[$keep]
            );
        }

        $str = $tokens;
    }

    /**
     * Execute the TRANSFORM mode.
     *
     * @param string $str The string to be tokenized
     */
    protected function replace(array &$str, $pattern, $replacement)
    {
        foreach ($str as &$s) {
            $s = preg_replace($pattern, $replacement, $s);
        }
    }
}
