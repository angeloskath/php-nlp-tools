<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

/**
 * A Document that represents a single word but with a context of a
 * larger document. Useful for Named Entity Recognition
 */
class WordDocument implements DocumentInterface
{
    protected string $word;

    /**
     * @var array<int, string>
     */
    protected array $before = [];

    /**
     * @var array<int, string>
     */
    protected array $after = [];

    /**
     * @param array<int, string> $tokens
     */
    public function __construct(array $tokens, int $index, int $context)
    {
        $this->word = $tokens[$index];
        for ($start = max($index - $context, 0); $start < $index; $start++) {
            $this->before[] = $tokens[$start];
        }

        $end = min($index + $context + 1, count($tokens));
        for ($start = $index + 1; $start < $end; $start++) {
            $this->after[] = $tokens[$start];
        }
    }

    /**
     * It returns an array with the first element being the actual word,
     * the second element being an array of previous words, and the
     * third an array of following words
     *
     * @return array<int, mixed>
     */
    public function getDocumentData(): array
    {
        return [$this->word, $this->before, $this->after];
    }

    /**
     * Apply the transformation to the token and the surrounding context.
     * Filter out the null tokens from the context. If the word is transformed
     * to null it is for the feature factory to decide what to do.
     *
     * @param TransformationInterface $transformation The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transformation): void
    {
        $null_filter = fn($token): bool => $token !== null;

        $this->word = $transformation->transform($this->word);
        // array_values for re-indexing
        $this->before = array_values(
            array_filter(
                array_map(
                    $transformation->transform(...),
                    $this->before
                ),
                $null_filter
            )
        );
        $this->after = array_values(
            array_filter(
                array_map(
                    $transformation->transform(...),
                    $this->after
                ),
                $null_filter
            )
        );
    }

    public function getClass(): string
    {
        return self::class;
    }
}
