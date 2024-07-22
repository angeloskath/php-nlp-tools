<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

interface TokenizerInterface
{
    /**
     * Break a character sequence to a token sequence
     *
     * @param  string $str The text for tokenization
     * @return array<int, mixed>  The list of tokens from the string
     */
    public function tokenize(string $str): array;
}
