<?php

namespace NlpTools\Tokenizers;

interface TokenizerInterface
{
    /**
     * Break a character sequence to a token sequence
     *
     * @param  string $str The text for tokenization
     * @return array  The list of tokens from the string
     */
    public function tokenize($str);
}
