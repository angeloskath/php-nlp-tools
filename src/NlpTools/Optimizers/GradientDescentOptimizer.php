<?php

declare(strict_types=1);

namespace NlpTools\Optimizers;

/**
 * Implements gradient descent with fixed step.
 * Leaves the computation of the fprime to the children classes.
 */
abstract class GradientDescentOptimizer implements FeatureBasedLinearOptimizerInterface
{
    /**
     * Array that holds the current fprime
     *
     * @var array<string, float>
     */
    protected array $fprimeVector;

    // report the improvement
    protected int $verbose = 2;

    public function __construct(protected float $precision = 0.001, protected float $step = 0.1, protected int $maxiter = -1)
    {
    }

    /**
     * Should initialize the weights and compute any constant
     * expressions needed for the fprime calculation.
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @param array<string, mixed> $l The current set of weights to be initialized
     */
    abstract protected function initParameters(array &$featureArray, array &$l): void;

    /**
     * Should calculate any parameter needed by Fprime that cannot be
     * calculated by initParameters because it is not constant.
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @param array<string, mixed> $l The current set of weights to be initialized
     */
    abstract protected function prepareFprime(array &$featureArray, array &$l): void;

    /**
     * Actually compute the fprime_vector. Set for each $l[$i] the
     * value of the partial derivative of f for delta $l[$i]
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @param array<string, mixed> $l The current set of weights to be initialized
     */
    abstract protected function fPrime(array &$featureArray, array &$l): void;

    /**
     * Actually do the gradient descent algorithm.
     * l[i] = l[i] - learning_rate*( theta f/delta l[i] ) for each i
     * Could possibly benefit from a vetor add/scale function.
     *
     * @param array<string, mixed> $featureArray All the data known about the training set
     * @return array<string, mixed> The parameters $l[$i] that minimize F
     */
    public function optimize(array &$featureArray): array
    {
        $itercount = 0;
        $optimized = false;
        $maxiter = $this->maxiter;
        $prec = $this->precision;
        $step = $this->step;
        $l = [];
        $this->initParameters($featureArray, $l);
        while (!$optimized && $itercount++ != $maxiter) {
            //$start = microtime(true);
            $optimized = true;
            $this->prepareFprime($featureArray, $l);
            $this->fPrime($featureArray, $l);
            foreach ($this->fprimeVector as $i => $fprime_i_val) {
                $l[$i] -= $step * $fprime_i_val;
                if (abs($fprime_i_val) > $prec) {
                    $optimized = false;
                }
            }

            if ($this->verbose > 0) {
                $this->reportProgress($itercount);
            }
        }

        return $l;
    }

    public function reportProgress(int $iterCount): void
    {
        if ($iterCount === 1) {
            echo "#\t|Fprime|\n------------------\n";
        }

        $norm = 0;
        foreach ($this->fprimeVector as $fprimeIval) {
            $norm += $fprimeIval * $fprimeIval;
        }

        $norm = sqrt($norm);
        printf("%d\t%.3f\n", $iterCount, $norm);
    }
}
