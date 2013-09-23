<?php

namespace NlpTools\Documents;

/**
 * Represents a bag of words (tokens) document.
 * @author angeloskath
 * @author Dan Cardin (yooper)
 */
class TokensDocument implements DocumentInterface
{
    /**
     * An array of tokens
     * @var array
     */
    protected $tokens = null;
    
    /**
     *
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }
    /**
     * Simply return the tokens received in the constructor
     * @return array The tokens array
     */
    public function getDocumentData()
    {
        return $this->tokens;
    }
    
}
