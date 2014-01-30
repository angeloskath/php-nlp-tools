<?php

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

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
     * Apply the transform to each token. Filter out the null tokens.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        // array_values for re-indexing
        $this->tokens = array_values(
            array_filter(
                array_map(
                    array($transform, 'transform'),
                    $this->tokens
                ),
                function ($token) {
                    return $token!==null;
                }
            )
        );
    }
}
