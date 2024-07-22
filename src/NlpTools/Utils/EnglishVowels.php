<?php

declare(strict_types=1);

namespace NlpTools\Utils;

/**
 * Helper Vowel class, determines if the character at a given index is a vowel
 * @author Dan Cardin
 */
class EnglishVowels extends VowelsAbstractFactory
{
    /**
     * Returns true if the letter at the given index is a vowel, works with y
     * @param  string  $word  the word to use
     * @param  int     $index the index in the string to inspect
     * @return boolean True letter at the provided index is a vowel
     */
    public function isVowel(string $word, int $index): bool
    {
        if (strpbrk($word[$index], 'aeiou') !== false) {
            return true;
        }

        return $word[$index] === 'y' && strpbrk($word[--$index], 'aeiou') === false;
    }
}
