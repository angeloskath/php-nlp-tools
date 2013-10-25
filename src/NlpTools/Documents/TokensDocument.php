<?php

namespace NlpTools\Documents;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;

/**
 * Represents a bag of words (tokens) document.
 */
class TokensDocument implements DocumentInterface
{
    protected $tokens;
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

    /**
     * Applies a transformation object to the tokens in this document
     * @param TokenTransformationInterface $transformer
     */
    public function applyTransformation(TokenTransformationInterface $transformer)
    {
        foreach ($this->tokens as &$token) {
            $token = $transformer->transform($token);
        }

        // filter the null tokens and re-index the array
        $this->tokens = array_values(array_filter($this->tokens));
    }

}
