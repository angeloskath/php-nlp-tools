<?php

declare(strict_types=1);

namespace NlpTools\Stemmers;

use NlpTools\Utils\TransformationInterface;

/**
 * http://en.wikipedia.org/wiki/Stemming
 */
abstract class Stemmer implements TransformationInterface
{
    /**
     * Remove the suffix from $word
     */
    abstract public function stem(string $word): string;

    /**
     * Apply the stemmer to every single token.
     *
     * @param array<int, string> $tokens
     * @return array<int, string>
     */
    public function stemAll(array $tokens): array
    {
        return array_map($this->stem(...), $tokens);
    }

    /**
     * A stemmer's transformation is simply the replacing of a word
     * with its stem.
     */
    public function transform(string $word): ?string
    {
        return $this->stem($word);
    }
}
