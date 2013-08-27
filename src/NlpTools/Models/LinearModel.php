<?php

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
    protected $l;
    public function __construct(array $l)
    {
        $this->l = $l;
    }
    /**
     * Get the weight for a given feature
     *
     * @param  string $feature The feature for which the weight will be returned
     * @return float  The weight
     */
    public function getWeight($feature)
    {
        if (!isset($this->l[$feature])) return 0;
        else return $this->l[$feature];
    }

    /**
     * Get all the weights as an array.
     *
     * @return array The weights as an associative array
     */
    public function getWeights()
    {
        return $this->l;
    }
}
