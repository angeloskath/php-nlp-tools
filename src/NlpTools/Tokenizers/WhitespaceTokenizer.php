<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

/**
 * Simple white space tokenizer.
 * Break on every white space
 */
class WhitespaceTokenizer implements TokenizerInterface
{
    public const PATTERN = '/[\pZ\pC]+/u';

    public function tokenize(string $str): array
    {
        return preg_split(self::PATTERN, $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
