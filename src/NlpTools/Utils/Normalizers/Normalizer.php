<?php

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
     * @return string
     */
    abstract public function normalize($w);

    /**
     * {@inheritdoc}
     */
    public function transform($w)
    {
        return $this->normalize($w);
    }

    /**
     * Apply the normalize function to all the items in the array
     * @param  array $items
     * @return array
     */
    public function normalizeAll(array $items)
    {
        return array_map(
            array($this, 'normalize'),
            $items
        );
    }

    /**
     * Just instantiate the normalizer using a factory method.
     * Keep in mind that this is NOT required. The constructor IS
     * visible.
     *
     * @param string $language
     */
    public static function factory($language = "English")
    {
        $classname = __NAMESPACE__."\\$language";

        return new $classname();
    }
}
