<?php

namespace NlpTools\Classifiers;

use \NlpTools\Documents\DocumentInterface;
use \NlpTools\FeatureFactories\FeatureFactoryInterface;
use \NlpTools\Models\LinearModel;

/**
 * Classify using a linear model. A model that assigns a weight l for
 * each feature f.
 */
class FeatureBasedLinearClassifier implements ClassifierInterface
{
    // The feature factory
    protected $feature_factory;
    // The LinearModel
    protected $model;

    public function __construct(FeatureFactoryInterface $ff, LinearModel $m)
    {
        $this->feature_factory = $ff;
        $this->model = $m;
    }

    /**
     * Compute the vote for every class. Return the class that
     * receive the maximum vote.
     *
     * @param  array             $classes A set of classes
     * @param  DocumentInterface $d       A Document
     * @return string            A class
     */
    public function classify(array $classes, DocumentInterface $d)
    {
        $maxclass = current($classes);
        $maxvote = $this->getVote($maxclass,$d);
        while ($class = next($classes)) {
            $v = $this->getVote($class,$d);
            if ($v>$maxvote) {
                $maxclass = $class;
                $maxvote = $v;
            }
        }

        return $maxclass;
    }

    /**
     * Compute the features that fire for the Document $d. The sum of
     * the weights of the features is the vote.
     *
     * @param  string            $class The vote for class $class
     * @param  DocumentInterface $d     The vote for Document $d
     * @return float             The vote of the model for class $class and Document $d
     */
    public function getVote($class, DocumentInterface $d)
    {
        $v = 0;
        $features = $this->feature_factory->getFeatureArray($class,$d);
        foreach ($features as $f) {
            $v += $this->model->getWeight($f);
        }

        return $v;
    }
}
