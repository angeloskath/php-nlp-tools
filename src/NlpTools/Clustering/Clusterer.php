<?php

namespace NlpTools\Clustering;

use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\TrainingSet;

abstract class Clusterer
{
    /**
     * Group the documents together
     *
     * @param  TrainingSet             $documents The documents to be clustered
     * @param  FeatureFactoryInterface $ff        A feature factory to transform the documents given
     * @return array                   The clusters, an array containing arrays of offsets for the documents
     */
    abstract public function cluster(TrainingSet $documents, FeatureFactoryInterface $ff);

    /**
     * Helper function to transform a TrainingSet to an array of feature vectors
     */
    protected function getDocumentArray(TrainingSet $documents, FeatureFactoryInterface $ff)
    {
        $docs = array();
        foreach ($documents as $d) {
            $docs[] = $ff->getFeatureArray('',$d);
        }

        return $docs;
    }
}
