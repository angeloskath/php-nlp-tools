<?php

namespace NlpTools\Tokenizers;

/**
 * Simple white space tokenizer.
 * Break on every white space
 */
class WhitespaceTokenizer implements TokenizerInterface
{
    const PATTERN = '/[\pZ\pC]+/u';

    public function tokenize($str)
    {
        $arr = array();

        return preg_split(self::PATTERN,$str,null,PREG_SPLIT_NO_EMPTY);
    }
}
