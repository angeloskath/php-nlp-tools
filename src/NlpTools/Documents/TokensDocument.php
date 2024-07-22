<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

/**
 * Represents a bag of words (tokens) document.
 */
class TokensDocument implements DocumentInterface
{
    /**
     * @param array<int|string, mixed> $tokens
     */
    public function __construct(protected array $tokens)
    {
    }

    /**
     * Simply return the tokens received in the constructor
     *
     * @return array<int, string>
     */
    public function getDocumentData(): array
    {
        return $this->tokens;
    }

    /**
     * Apply the transform to each token. Filter out the null tokens.
     *
     * @param TransformationInterface $transformation The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transformation): void
    {
        // array_values for re-indexing
        $this->tokens = array_values(
            array_filter(
                array_map(
                    $transformation->transform(...),
                    $this->tokens
                ),
                fn($token): bool => $token !== null
            )
        );
    }

    public function getClass(): string
    {
        return self::class;
    }
}
