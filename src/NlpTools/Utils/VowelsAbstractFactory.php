<?php

declare(strict_types=1);

namespace NlpTools\Utils;

/**
 * Factory wrapper for Vowels
 * @author Dan Cardin
 */
abstract class VowelsAbstractFactory
{
    /**
     * Return the correct language vowel checker
     * @throws \Exception
     */
    public static function factory(string $language = 'English'): self
    {
        $className = "\\" . __NAMESPACE__ . sprintf('\%sVowels', $language);
        if (class_exists($className)) {
            return new $className();
        }

        throw new \Exception(sprintf('Class %s does not exist', $className));
    }

    /**
     * Check if the the letter at the given index is a vowel
     */
    abstract public function isVowel(string $word, int $index): bool;
}
