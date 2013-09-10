<?php

namespace NlpTools\Optimizers;

interface FeatureBasedLinearOptimizerInterface
{
    /**
     * This function receives an array that contains an array for
     * each document which contains an array of feature identifiers for
     * each class and at the special key '__label__' the actual class
     * of the training document.
     *
     * As a result it contains all the information needed to train a
     * set of weights with any target. Ex.: If we were training a maxent
     * model we would try to maximize the CLogLik that can be calculated
     * from this array.
     *
     * @param  array &$feature_array
     * @return array The parameteres $l
     */
    public function optimize(array &$feature_array);
}
