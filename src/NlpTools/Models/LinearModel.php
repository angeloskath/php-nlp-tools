<?php

namespace NlpTools\Models;

use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\DocumentInterface;

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

    /**
     * Compute the features that fire for the Document $d. The sum of
     * the weights of the features is the vote.
     *
     * @param  string                  $class The vote for class $class
     * @param  FeatureFactoryInterface $ff    The feature factory
     * @param  DocumentInterface       $d     The vote for Document $d
     * @return float                   The vote of the model for class $class and Document $d
     */
    public function getVote($class, FeatureFactoryInterface $ff, DocumentInterface $d)
    {
        $v = 0;
        $features = $ff->getFeatureArray($class,$d);
        foreach ($features as $f=>$v) {
            if (isset($this->l[$f]))
                $v += $this->l[$f];
        }

        return $v;
    }
}
