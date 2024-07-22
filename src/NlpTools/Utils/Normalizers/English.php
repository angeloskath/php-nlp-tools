<?php

declare(strict_types=1);

namespace NlpTools\Utils\Normalizers;

/**
 * For English we simply transform to lower case using mb_strtolower.
 * This should be used as a fallback for any language since mb_strtolower
 * will do at least half good a job
 */
class English extends Normalizer
{
    public function normalize(string $w): string
    {
        return mb_strtolower($w, "utf-8");
    }
}
