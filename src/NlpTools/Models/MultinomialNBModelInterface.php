<?php

namespace NlpTools\Models;

/**
 * Interface that describes a NB model.
 * All that we need is the prior probability of a class
 * and the conditional probability of a term given a class.
 */
interface MultinomialNBModelInterface
{
    public function getPrior($class);
    public function getCondProb($term,$class);
}
