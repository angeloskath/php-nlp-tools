<?php

namespace NlpTools\Optimizers;

/**
 * Implements gradient descent with fixed step.
 * Leaves the computation of the fprime to the children classes.
 */
abstract class GradientDescentOptimizer implements FeatureBasedLinearOptimizerInterface
{
    // gradient descent parameters
    protected $precision; // how close to zero should fprime go
    protected $step; // learning rate
    protected $maxiter; // maximum iterations (-1 for "infinite")

    // array that holds the current fprime
    protected $fprime_vector;

    // report the improvement
    protected $verbose=2;

    public function __construct($precision=0.001, $step=0.1, $maxiter = -1)
    {
        $this->precision = $precision;
        $this->step = $step;
        $this->maxiter = $maxiter;
    }

    /**
     * Should initialize the weights and compute any constant
     * expressions needed for the fprime calculation.
     *
     * @param $feature_array All the data known about the training set
     * @param $l The current set of weights to be initialized
     * @return void
     */
    abstract protected function initParameters(array &$feature_array, array &$l);
    /**
     * Should calculate any parameter needed by Fprime that cannot be
     * calculated by initParameters because it is not constant.
     *
     * @param $feature_array All the data known about the training set
     * @param $l The current set of weights to be initialized
     * @return void
     */
    abstract protected function prepareFprime(array &$feature_array, array &$l);
    /**
     * Actually compute the fprime_vector. Set for each $l[$i] the
     * value of the partial derivative of f for delta $l[$i]
     *
     * @param $feature_array All the data known about the training set
     * @param $l The current set of weights to be initialized
     * @return void
     */
    abstract protected function Fprime(array &$feature_array, array &$l);

    /**
     * Actually do the gradient descent algorithm.
     * l[i] = l[i] - learning_rate*( theta f/delta l[i] ) for each i
     * Could possibly benefit from a vetor add/scale function.
     *
     * @param $feature_array All the data known about the training set
     * @return array The parameters $l[$i] that minimize F
     */
    public function optimize(array &$feature_array)
    {
        $itercount = 0;
        $optimized = false;
        $maxiter = $this->maxiter;
        $prec = $this->precision;
        $step = $this->step;
        $l = array();
        $this->initParameters($feature_array,$l);
        while (!$optimized && $itercount++!=$maxiter) {
            //$start = microtime(true);
            $optimized = true;
            $this->prepareFprime($feature_array,$l);
            $this->Fprime($feature_array,$l);
            foreach ($this->fprime_vector as $i=>$fprime_i_val) {
                $l[$i] -= $step*$fprime_i_val;
                if (abs($fprime_i_val) > $prec) {
                    $optimized = false;
                }
            }
            //fprintf(STDERR,"%f\n",microtime(true)-$start);
            if ($this->verbose>0)
                $this->reportProgress($itercount);
        }

        return $l;
    }

    public function reportProgress($itercount)
    {
        if ($itercount == 1) {
            echo "#\t|Fprime|\n------------------\n";
        }
        $norm = 0;
        foreach ($this->fprime_vector as $fprime_i_val) {
            $norm += $fprime_i_val*$fprime_i_val;
        }
        $norm = sqrt($norm);
        printf("%d\t%.3f\n",$itercount,$norm);
    }
}
