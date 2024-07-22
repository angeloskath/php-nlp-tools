<?php

declare(strict_types=1);

namespace NlpTools\Utils\Normalizers;

/**
 * To normalize greek text we use mb_strtolower to transform
 * to lower case and then replace every accented character
 * with its non-accented counter part and the final ς with σ
 */
class Greek extends Normalizer
{
    /**
     * @var array<int, string>
     */
    protected static array $dirty = ['ά', 'έ', 'ό', 'ή', 'ί', 'ύ', 'ώ', 'ς'];

    /**
     * @var array<int, string>
     */
    protected static array $clean = ['α', 'ε', 'ο', 'η', 'ι', 'υ', 'ω', 'σ'];

    public function normalize(string $w): string
    {
        return str_replace(self::$dirty, self::$clean, mb_strtolower($w, "utf-8"));
    }
}
