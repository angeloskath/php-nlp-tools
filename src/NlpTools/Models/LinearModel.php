<?php

declare(strict_types=1);

namespace NlpTools\Models;

/**
 * This class represents a linear model of the following form
 * f(x_vec) = l1*x1 + l2*x2 + l3*x3 ...
 *
 * Maybe the name is a bit off. What is really meant is that models of
 * this type provide a set of weights that will be used by the classifier
 * (probably through a linear combination) to decide the class of a
 * given document.
 *
 */
class LinearModel
{
    /**
     * @param array<string, float> $l
     */
    public function __construct(protected array $l)
    {
    }

    /**
     * Get the weight for a given feature
     *
     * @param  string $feature The feature for which the weight will be returned
     * @return float  The weight
     */
    public function getWeight(string $feature): float
    {
        if (!isset($this->l[$feature])) {
            return 0;
        }

        return $this->l[$feature];
    }

    /**
     * Get all the weights as an array.
     *
     * @return array<string, float> The weights as an associative array
     */
    public function getWeights(): array
    {
        return $this->l;
    }
}
