<?php

namespace NlpTools\Classifiers;

use NlpTools\Documents\TrainingSet;

/**
 * Classify a sequence.
 */
interface SequenceClassifierInterface
{
    /**
     * @return array The array of labels best matching the whole sequence of documents
     */
    public function classify(array $classes, TrainingSet $docs)
}
