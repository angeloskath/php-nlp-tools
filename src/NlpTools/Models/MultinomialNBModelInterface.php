<?php

declare(strict_types=1);

namespace NlpTools\Models;

/**
 * Interface that describes a NB model.
 * All that we need is the prior probability of a class
 * and the conditional probability of a term given a class.
 */
interface MultinomialNBModelInterface
{
    public function getPrior(string $class): float;

    public function getCondProb(string $term, string $class): float;
}
