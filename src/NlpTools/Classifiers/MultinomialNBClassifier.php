<?php

declare(strict_types=1);

namespace NlpTools\Classifiers;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Models\MultinomialNBModelInterface;

/**
 * Use a multinomia NB model to classify a document
 */
class MultinomialNBClassifier implements ClassifierInterface
{
    public function __construct(protected FeatureFactoryInterface $featureFactory, protected MultinomialNBModelInterface $multinomialNBModel)
    {
    }

    /**
     * Compute the probability of $d belonging to each class
     * successively and return that class that has the maximum
     * probability.
     *
     * @param array<int, string> $classes
     */
    public function classify(array $classes, DocumentInterface $document): string
    {
        $maxclass = current($classes);
        $maxscore = $this->getScore($maxclass, $document);
        while ($class = next($classes)) {
            $score = $this->getScore($class, $document);
            if ($score > $maxscore) {
                $maxclass = $class;
                $maxscore = $score;
            }
        }

        return $maxclass;
    }

    /**
     * Compute the log of the probability of the Document $d belonging
     * to class $class. We compute the log so that we can sum over the
     * logarithms instead of multiplying each probability.
     *
     * @todo perhaps MultinomialNBModel should have precomputed the logs
     *       ex.: getLogPrior() and getLogCondProb()
     */
    public function getScore(string $class, DocumentInterface $document): float
    {
        $score = log($this->multinomialNBModel->getPrior($class));
        $features = $this->featureFactory->getFeatureArray($class, $document);
        if (is_int(key($features))) {
            $features = array_count_values($features);
        }

        foreach ($features as $f => $fcnt) {
            $score += $fcnt * log($this->multinomialNBModel->getCondProb($f, $class));
        }

        return $score;
    }
}
