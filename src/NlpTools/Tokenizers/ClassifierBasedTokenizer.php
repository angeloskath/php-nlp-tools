<?php

namespace NlpTools\Tokenizers;

use \NlpTools\Classifiers\ClassifierInterface;
use \NlpTools\Documents\WordDocument;

/**
 * A tokenizer that uses a classifier (of any type) to determine if
 * there is an "end of word" (EOW). It takes as a parameter an initial
 * tokenizer and then determines if any two following tokens should in
 * fact be one token.
 *
 * Those tokenizers could be nested to produce sentence tokenizers.
 *
 * Example:
 *
 * If we were for example to tokenize the following sentence
 * "Me and O'Brien, we 'll go!" and we used a simple space tokenizer we
 * would end up with this
 * ["Me","and","O'Brien,","we","'ll","go!"]
 * if we used a space and punctuation tokenizer we 'd end up with
 * ["Me","and","O","'","Brien",",","we","'","ll","go","!"]
 * but we want
 * ["Me","and","O'Brien",",","we","'ll","go","!"]
 * so we should train a classifier to do the following
 *
 * Token | Cls
 * ------------
 * Me    | EOW
 * and   | EOW
 * O     | O
 * '     | O
 * Brien | EOW
 * ,     | EOW
 * we    | EOW
 * '     | O
 * ll    | EOW
 * go    | EOW
 * !     | EOW
 *
 */
class ClassifierBasedTokenizer implements TokenizerInterface
{
    const EOW = 'EOW';
    protected static $classSet = array('O','EOW');

    // initial tokenizer
    protected $tok;

    protected $classifier;

    // used when joining the tokens into one
    protected $sep;

    public function __construct(ClassifierInterface $cls, TokenizerInterface $tok=null,$sep=' ')
    {
        if ($tok == null) {
            $this->tok = new WhitespaceAndPunctuationTokenizer();
        } else {
            $this->tok  = $tok;
        }
        $this->classifier = $cls;
        $this->sep = $sep;
    }

    /**
     * Tokenize the string.
     *
     * 1. Break up the string in tokens using the initial tokenizer
     * 2. Classify each token if it is an EOW
     * 3. For each token that is not an EOW add it to the next EOW token using a separator
     *
     * @param  string $str The character sequence to be broken in tokens
     * @return array  The token array
     */
    public function tokenize($str)
    {
        // split the string in tokens and create documents to be
        // classified
        $tokens = $this->tok->tokenize($str);
        $docs = array();
        foreach ($tokens as $offset=>$tok) {
            $docs[] = new WordDocument($tokens,$offset,5);
        }

        // classify each token as an EOW or O
        $tags = array();
        foreach ($docs as $doc) {
            $tags[] = $this->classifier->classify(self::$classSet, $doc);
        }

        // merge O and EOW into real tokens
        $realtokens = array();
        $currentToken = array();
        foreach ($tokens as $offset=>$tok) {
            $currentToken[] = $tok;
            if ($tags[$offset] == self::EOW) {
                $realtokens[] = implode($this->sep,$currentToken);
                $currentToken = array();
            }
        }

        // return real tokens
        return $realtokens;
    }
}
