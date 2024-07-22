<?php

declare(strict_types=1);

namespace NlpTools\Clustering;

use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\TrainingSet;

abstract class Clusterer
{
    /**
     * Group the documents together
     *
     * @param TrainingSet $trainingSet The documents to be clustered
     * @param FeatureFactoryInterface $featureFactory A feature factory to transform the documents given
     * @return array<int, mixed>       The clusters, an array containing arrays of offsets for the documents
     */
    abstract public function cluster(TrainingSet $trainingSet, FeatureFactoryInterface $featureFactory): array;

    /**
     * Helper function to transform a TrainingSet to an array of feature vectors
     *
     * @return array<int, mixed>
     */
    protected function getDocumentArray(TrainingSet $trainingSet, FeatureFactoryInterface $featureFactory): array
    {
        $docs = [];
        foreach ($trainingSet as $d) {
            $docs[] = $featureFactory->getFeatureArray('', $d);
        }

        return $docs;
    }
}
