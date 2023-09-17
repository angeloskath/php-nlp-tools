<?php

namespace NlpTools\Utils\Normalizers;

class French extends Normalizer
{
    protected static $dirty = array(
        'à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï',
        'ô', 'ö', 'ù', 'û', 'ü', 'ÿ', 'œ', 'æ', 'ç'
    );

    protected static $clean = array(
        'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i',
        'o', 'o', 'u', 'u', 'u', 'y', 'oe', 'ae', 'c'
    );

    public function normalize($w)
    {
        return str_replace(self::$dirty, self::$clean, \mb_strtolower($w, "utf-8"));
    }
}
