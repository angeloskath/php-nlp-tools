<?php

declare(strict_types=1);

namespace NlpTools\Random\Generators;

/**
 * A simple wrapper over the built in mt_rand() method
 */
class MersenneTwister implements GeneratorInterface
{
    public function generate(): float
    {
        return mt_rand() / mt_getrandmax();
    }

    protected static ?MersenneTwister $instance = null;

    public static function get(): self
    {
        if (self::$instance instanceof MersenneTwister) {
            return self::$instance;
        }

        self::$instance = new MersenneTwister();

        return self::$instance;
    }
}
