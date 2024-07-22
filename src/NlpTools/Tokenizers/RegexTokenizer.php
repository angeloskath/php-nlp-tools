<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

/**
 * Regex tokenizer tokenizes text based on a set of regexes
 */
class RegexTokenizer implements TokenizerInterface
{
    /**
     * Initialize the Tokenizer
     *
     * @param array<int, mixed> $patterns The regular expressions
     */
    public function __construct(protected array $patterns)
    {
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
     * @return array<int, mixed>  The tokens
     */
    public function tokenize(string $str): array
    {
        $str = [$str];
        foreach ($this->patterns as $pattern) {
            if (!is_array($pattern)) {
                $pattern = [$pattern];
            }

            if (count($pattern) === 1) { // split pattern
                $this->split($str, $pattern[0]);
            } elseif (is_int($pattern[1])) { // match pattern
                $this->match($str, $pattern[0], (string) $pattern[1]);
            } else { // replace pattern
                $this->replace($str, $pattern[0], $pattern[1]);
            }
        }

        return $str;
    }

    /**
     * Execute the SPLIT mode
     *
     * @param array<int, mixed> &$str The tokens to be further tokenized
     */
    protected function split(array &$str, string $pattern): void
    {
        $tokens = [];
        foreach ($str as $s) {
            $tokens = array_merge(
                $tokens,
                preg_split($pattern, (string) $s, -1, PREG_SPLIT_NO_EMPTY)
            );
        }

        $str = $tokens;
    }

    /**
     * Execute the KEEP_MATCHES mode
     *
     * @param array<int, mixed> &$str The tokens to be further tokenized
     */
    protected function match(array &$str, string $pattern, string $keep): void
    {
        $tokens = [];
        foreach ($str as $s) {
            preg_match_all($pattern, (string) $s, $m);
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
     * @param array<int, mixed> &$str The tokens to be further tokenized
     */
    protected function replace(array &$str, string $pattern, string $replacement): void
    {
        foreach ($str as &$s) {
            $s = preg_replace($pattern, $replacement, $s);
        }
    }
}
