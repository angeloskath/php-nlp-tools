<?php

declare(strict_types=1);

namespace NlpTools\Analysis;

use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\FeatureFactories\DataAsFeatures;

/**
 * Idf implements the inverse document frequency measure.
 * Idf is a measure of whether a term T is common or rare accross
 * a set of documents.
 *
 * Idf implements the ArrayAccess interface so it should be used
 * as a read only array that contains tokens as keys and idf values
 * as values.
 *
 * @implements \ArrayAccess<mixed, mixed>
 */
class Idf implements \ArrayAccess
{
    protected float $logD;

    /**
     * @var array<mixed, mixed>
     */
    protected array $idf;

    /**
     * @param TrainingSet $trainingSet The set of documents for which we will compute the idf
     * @param FeatureFactoryInterface $featureFactory A feature factory to translate the document data to single tokens
     */
    public function __construct(TrainingSet $trainingSet, FeatureFactoryInterface $featureFactory = null)
    {
        if (!$featureFactory instanceof FeatureFactoryInterface) {
            $featureFactory = new DataAsFeatures();
        }

        $trainingSet->setAsKey(TrainingSet::CLASS_AS_KEY);
        foreach ($trainingSet as $class => $doc) {
            $tokens = $featureFactory->getFeatureArray($class, $doc); // extract tokens from the document
            $tokens = array_fill_keys($tokens, 1); // make them occur once
            foreach (array_keys($tokens) as $token) {
                if (isset($this->idf[$token])) {
                    $this->idf[$token]++;
                } else {
                    $this->idf[$token] = 1;
                }
            }
        }

        // this idf so far contains the doc frequency
        // we will now inverse it and take the log
        $D = count($trainingSet);
        foreach ($this->idf as &$v) {
            $v = log($D / $v);
        }

        $this->logD = log($D);
    }

    /**
     * Implements the array access interface. Return the computed idf or
     * the logarithm of the count of the documents for a token we have not
     * seen before.
     */
    public function offsetGet(mixed $token): mixed
    {
        return $this->idf[$token] ?? $this->logD;
    }

    /**
     * Implements the array access interface. Return true if the token exists
     * in the corpus.
     */
    public function offsetExists(mixed $token): bool
    {
        return isset($this->idf[$token]);
    }

    /**
     * Will not be implemented. Throws \BadMethodCallException because
     * one should not be able to alter the idf values directly.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException("The idf of a specific token cannot be set explicitly");
    }

    /**
     * Will not be implemented. Throws \BadMethodCallException because
     * one should not be able to alter the idf values directly.
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException("The idf of a specific token cannot be unset");
    }
}
