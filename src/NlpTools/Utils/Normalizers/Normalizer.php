<?php

declare(strict_types=1);

namespace NlpTools\Utils\Normalizers;

use NlpTools\Utils\TransformationInterface;

/**
 * The Normalizer's purpose is to transform any word from any
 * one of the possible writings to a single writing consistently.
 * A lot of algorithms for stemming already expect normalized text.
 *
 * The most common normalization would be to transform the words to
 * lower case. There are languages though that this is not enough
 * since there maybe other diacritics that need to be removed.
 *
 * E.g.: The         -> the
 *       I           -> i
 *       WhAtEvEr    -> whatever
 *       Άγγελος     -> αγγελοσ
 *       Αριστοτέλης -> αριστοτελησ
 */
abstract class Normalizer implements TransformationInterface
{
    /**
     * Transform the word according to the class description
     *
     * @param  string $w The word to normalize
     */
    abstract public function normalize(string $w): ?string;

    /**
     * {@inheritdoc}
     */
    public function transform(string $w): ?string
    {
        return $this->normalize($w);
    }

    /**
     * Apply the normalize function to all the items in the array
     *
     * @param array<int, string> $items
     * @return array<int, string>
     */
    public function normalizeAll(array $items): array
    {
        return array_map(
            $this->normalize(...),
            $items
        );
    }

    /**
     * Just instantiate the normalizer using a factory method.
     * Keep in mind that this is NOT required. The constructor IS
     * visible.
     */
    public static function factory(string $language = "English"): self
    {
        $classname = __NAMESPACE__ . ('\\' . $language);

        return new $classname();
    }
}
