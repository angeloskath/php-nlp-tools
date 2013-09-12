<?php

namespace NlpTools\Random\Generators;

/**
 * An interface for pseudo-random number generators.
 *
 * @author Katharopoulos Angelos <angelos@yourse.gr>
 */
interface GeneratorInterface
{
    /**
     * Generates a pseudo-random number with uniform distribution in the
     * interval [0,1)
     *
     * @return float The "random" number
     */
    public function generate();
}
