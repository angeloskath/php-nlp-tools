<?php

namespace NlpTools\Models;

use \NlpTools\FeatureFactories\FeatureFactoryInterface;
use \NlpTools\Documents\TrainingSet;
use NlpTools\Optimizers\MaxentOptimizerInterface;

/**
 * Maxent is a model that assigns a weight for each feature such that all
 * the weights maximize the Conditional Log Likelihood of the training
 * data. Because it does that without making any assumptions about the data
 * it is named maximum entropy model (maximum ignorance).
 */
class Maxent extends LinearModel
{
    const INITIAL_PARAM_VALUE = 0;

    /**
     * Calculate all the features for every possible class. Pass the
     * information to the optimizer to find the weights that satisfy the
     * constraints and maximize the entropy
     *
     * @param $ff The feature factory
     * @param $tset A collection of training documents
     * @param $opt An optimizer, we need a maxent optimizer
     * @return void
     */
    public function train(FeatureFactoryInterface $ff, TrainingSet $tset, MaxentOptimizerInterface $opt)
    {
        $classSet = $tset->getClassSet();

        $features = $this->calculateFeatureArray($classSet,$tset,$ff);
        $this->l = $opt->optimize($features);
    }

    /**
     * Calculate all the features for each possible class of each
     * document. This is done so that we can optimize without the need
     * of the FeatureFactory.
     *
     * We do not want to use the FeatureFactoryInterface both because it would
     * be slow to calculate the features over and over again, but also
     * because we want to be able to optimize externally to
     * gain speed (PHP is slow!).
     *
     * @param $classes A set of the classes in the training set
     * @param $tset A collection of training documents
     * @param $ff The feature factory
     * @return array An array that contains every feature for every possible class of every document
     */
    protected function calculateFeatureArray(array $classes, TrainingSet $tset, FeatureFactoryInterface $ff)
    {
        $features = array();
        $tset->setAsKey(TrainingSet::OFFSET_AS_KEY);
        foreach ($tset as $offset=>$doc) {
            $features[$offset] = array();
            foreach ($classes as $class) {
                $features[$offset][$class] = $ff->getFeatureArray($class,$doc);
            }
            $features[$offset]['__label__'] = $doc->getClass();
        }

        return $features;
    }

    /**
     * Calculate the probability that document $d belongs to the class
     * $class given a set of possible classes, a feature factory and
     * the model's weights l[i]
     *
     * @param $classes The set of possible classes
     * @param $ff The feature factory
     * @param $d The document
     * @param  string $class A class for which we calculate the probability
     * @return float  The probability that document $d belongs to class $class
     */
    public function P(array $classes,FeatureFactoryInterface $ff,DocumentInterface $d,$class)
    {
        $exps = array();
        foreach ($classes as $cl) {
            $tmp = 0.0;
            foreach ($ff->getFeatureArray($cl,$d) as $i) {
                $tmp += $this->l[$i];
            }
            $exps[$cl] = exp($tmp);
        }

        return $exps[$class]/array_sum($exps);
    }

    /**
     * Not implemented yet.
     * Simply put:
     * 	result += log( $this->P(..., ..., ...) ) for every doc in TrainingSet
     *
     * @throws \Exception
     */
    public function CLogLik(TrainingSet $tset,FeatureFactoryInterface $ff)
    {
        throw new \Exception("Unimplemented");
    }

    /**
     * Simply print_r weights. Usefull for some kind of debugging when
     * working with small training sets and few features
     */
    public function dumpWeights()
    {
        print_r($this->l);
    }

}
