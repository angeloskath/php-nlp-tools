<?php

namespace NlpTools\Utils\Normalizers;

/**
 * To normalize greek text we use mb_strtolower to transform
 * to lower case and then replace every accented character
 * with its non-accented counter part and the final ς with σ
 */
class Greek extends Normalizer
{
    protected static $dirty = array(
        'ά','έ','ό','ή','ί','ύ','ώ','ς'
    );
    protected static $clean = array(
        'α','ε','ο','η','ι','υ','ω','σ'
    );

    public function normalize($w)
    {
        return str_replace(self::$dirty, self::$clean, mb_strtolower($w, "utf-8"));
    }
}
