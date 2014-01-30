<?php

namespace NlpTools\Random\Generators;

/**
 * A simple wrapper over the built in mt_rand() method
 */
class MersenneTwister implements GeneratorInterface
{
    public function generate()
    {
        return mt_rand()/mt_getrandmax();
    }

    protected static $instance;
    public static function get()
    {
        if (self::$instance!=null) return self::$instance;
        self::$instance = new MersenneTwister();

        return self::$instance;
    }
}
