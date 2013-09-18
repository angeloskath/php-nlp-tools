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
     * stores meta data, what the source name was
     * @var type 
     */
    protected $name = null;
    
    /**
     *
     * @param array $tokens
     * @param string $name A name assigned to this document
     */
    public function __construct(array $tokens, $name = null)
    {
        $this->tokens = $tokens;
        $this->name = $name;
    }
    /**
     * Simply return the tokens received in the constructor
     * @return array The tokens array
     */
    public function getDocumentData()
    {
        return $this->tokens;
    }
    
    /**
     * @return string|null Returns the name if it exists or null 
     */
    public function getName()
    {
        return $this->name;
    }
}
