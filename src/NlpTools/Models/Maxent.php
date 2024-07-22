<?php

declare(strict_types=1);

namespace NlpTools\Models;

use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\TrainingSet;
use NlpTools\Optimizers\MaxentOptimizerInterface;
use NlpTools\Documents\DocumentInterface;

/**
 * Maxent is a model that assigns a weight for each feature such that all
 * the weights maximize the Conditional Log Likelihood of the training
 * data. Because it does that without making any assumptions about the data
 * it is named maximum entropy model (maximum ignorance).
 */
class Maxent extends LinearModel
{
    public const INITIAL_PARAM_VALUE = 0;

    /**
     * Calculate all the features for every possible class. Pass the
     * information to the optimizer to find the weights that satisfy the
     * constraints and maximize the entropy
     */
    public function train(FeatureFactoryInterface $featureFactory, TrainingSet $trainingSet, MaxentOptimizerInterface $maxentOptimizer): void
    {
        $classSet = $trainingSet->getClassSet();

        $features = $this->calculateFeatureArray($classSet, $trainingSet, $featureFactory);
        $this->l = $maxentOptimizer->optimize($features);
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
     * @param array<int, string> $classes
     * @return array<int, mixed>
     */
    protected function calculateFeatureArray(array $classes, TrainingSet $trainingSet, FeatureFactoryInterface $featureFactory): array
    {
        $features = [];
        $trainingSet->setAsKey(TrainingSet::OFFSET_AS_KEY);
        foreach ($trainingSet as $offset => $doc) {
            $features[$offset] = [];
            foreach ($classes as $class) {
                $features[$offset][$class] = $featureFactory->getFeatureArray($class, $doc);
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
     * @param array<int, string> $classes
     */
    public function calculateProbability(array $classes, FeatureFactoryInterface $featureFactory, DocumentInterface $document, string $class): float
    {
        $exps = [];
        foreach ($classes as $cl) {
            $tmp = 0.0;
            foreach ($featureFactory->getFeatureArray($cl, $document) as $i) {
                $tmp += $this->l[$i];
            }

            $exps[$cl] = exp($tmp);
        }

        return $exps[$class] / array_sum($exps);
    }
}
