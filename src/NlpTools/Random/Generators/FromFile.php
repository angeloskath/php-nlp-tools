<?php

namespace NlpTools\Random\Generators;

/**
 * Return floats from a file. A useful generator for debugging algorithms
 * with random numbers from different platforms or different generation
 * algorithms.
 */
class FromFile implements GeneratorInterface
{
    protected $h;

    /**
     * Construct a FromFile generator
     * @param string $f A file name to read from
     */
    public function __construct($f)
    {
        $this->h = fopen($f,'r');
    }

    /**
     * Read a float from a file and return it. It doesn't do anything
     * to make sure that the float returned will be in the appropriate
     * range.
     *
     * If the file has reached its end it rewinds the file pointer.
     *
     * @return float A random float in the range (0,1)
     */
    public function generate()
    {
        if (feof($this->h))
            rewind($this->h);

        return (float) fgets($this->h);
    }
}
