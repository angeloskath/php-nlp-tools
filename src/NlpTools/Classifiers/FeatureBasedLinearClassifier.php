<?php

declare(strict_types=1);

namespace NlpTools\Classifiers;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Models\LinearModel;

/**
 * Classify using a linear model. A model that assigns a weight l for
 * each feature f.
 */
class FeatureBasedLinearClassifier implements ClassifierInterface
{
    public function __construct(protected FeatureFactoryInterface $featureFactory, protected LinearModel $linearModel)
    {
    }

    /**
     * Compute the vote for every class. Return the class that
     * receive the maximum vote.
     *
     * @param array<int, string> $classes
     */
    public function classify(array $classes, DocumentInterface $document): string
    {
        $maxclass = current($classes);
        $maxvote = $this->getVote($maxclass, $document);
        while ($class = next($classes)) {
            $v = $this->getVote($class, $document);
            if ($v > $maxvote) {
                $maxclass = $class;
                $maxvote = $v;
            }
        }

        return $maxclass;
    }

    /**
     * Compute the features that fire for the Document $d. The sum of
     * the weights of the features is the vote.
     */
    public function getVote(string $class, DocumentInterface $document): float
    {
        $v = 0;
        $features = $this->featureFactory->getFeatureArray($class, $document);
        foreach ($features as $feature) {
            $v += $this->linearModel->getWeight($feature);
        }

        return $v;
    }
}
