<?php

declare(strict_types=1);

namespace NlpTools\Optimizers;

/**
 * Implement a gradient descent algorithm that maximizes the conditional
 * log likelihood of the training data.
 *
 * See page 24 - 28 of http://nlp.stanford.edu/pubs/maxent-tutorial-slides.pdf
 * @see NlpTools\Models\Maxent
 */
class MaxentGradientDescent extends GradientDescentOptimizer implements MaxentOptimizerInterface
{
    /**
     * will hold the constant numerators
     *
     * @var array<string, mixed>
     */
    protected array $numerators;

    /**
     * denominators will be computed on each iteration because they
     * depend on the weights
     *
     * @var array<string, mixed>
     */
    protected array $denominators;

    /**
     * We initialize all weight for any feature we find to 0. We also
     * compute the empirical expectation (the count) for each feature in
     * the training data (which of course remains constant for a
     * specific set of data).
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @param array<string, mixed> $l The current set of weights to be initialized
     */
    protected function initParameters(array &$featureArray, array &$l): void
    {
        $this->numerators = [];
        $this->fprimeVector = [];
        foreach ($featureArray as $doc) {
            foreach ($doc as $features) {
                if (!is_array($features)) {
                    continue;
                }

                foreach ($features as $feature) {
                    $l[$feature] = 0;
                    $this->fprimeVector[$feature] = 0;
                    if (!isset($this->numerators[$feature])) {
                        $this->numerators[$feature] = 0;
                    }
                }
            }

            foreach ($doc[$doc['__label__']] as $fi) {
                $this->numerators[$fi]++;
            }
        }
    }

    /**
     * Compute the denominators which is the predicted expectation of
     * each feature given a set of weights L and a set of features for
     * each document for each class.
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @param array<string, mixed> $l The current set of weights to be initialized
     */
    protected function prepareFprime(array &$featureArray, array &$l): void
    {
        $this->denominators = [];
        foreach ($featureArray as $doc) {
            $numerator = array_fill_keys(array_keys($doc), 0.0);
            $denominator = 0.0;
            foreach ($doc as $cl => $f) {
                if (!is_array($f)) {
                    continue;
                }

                $tmp = 0.0;
                foreach ($f as $i) {
                    $tmp += $l[$i];
                }

                $tmp = exp($tmp);
                $numerator[$cl] += $tmp;
                $denominator += $tmp;
            }

            foreach ($doc as $class => $features) {
                if (!is_array($features)) {
                    continue;
                }

                foreach ($features as $feature) {
                    if (!isset($this->denominators[$feature])) {
                        $this->denominators[$feature] = 0;
                    }

                    $this->denominators[$feature] += $numerator[$class] / $denominator;
                }
            }
        }
    }

    /**
     * The partial Fprime for each i is
     * empirical expectation - predicted expectation . We need to
     * maximize the CLogLik (CLogLik is the f whose Fprime we calculate)
     * so we instead minimize the -CLogLik.
     *
     * See page 28 of http://nlp.stanford.edu/pubs/maxent-tutorial-slides.pdf
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @param array<string, mixed> $l The current set of weights to be initialized
     */
    protected function fPrime(array &$featureArray, array &$l): void
    {
        foreach ($this->fprimeVector as $i => &$fprime_i_val) {
            $fprime_i_val = $this->denominators[$i] - $this->numerators[$i];
        }
    }
}
