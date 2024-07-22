<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

/**
 * Simple white space tokenizer. Breaks either on whitespace or on word
 * boundaries (ex.: dots, commas, etc)
 * Does not include white space in tokens.
 * Every punctuation character is a signle token
 */
class WhitespaceAndPunctuationTokenizer implements TokenizerInterface
{
    public function tokenize(string $str): array
    {
        $arr = [];
        // for the character classes
        // see http://php.net/manual/en/regexp.reference.unicode.php
        $pat = '/
                    ([\pZ\pC]*)			# match any separator or other
                                        # in sequence
                    (
                        [^\pP\pZ\pC]+ |	# match a sequence of characters
                                        # that are not punctuation,
                                        # separator or other

                        .				# match punctuations one by one
                    )
                    ([\pZ\pC]*)			# match a sequence of separators
                                        # that follows
                /xu';
        preg_match_all($pat, $str, $arr);

        return $arr[2];
    }
}
