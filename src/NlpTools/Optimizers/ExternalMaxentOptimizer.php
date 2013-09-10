<?php

namespace NlpTools\Optimizers;

/**
 * This class enables the use of a program written in a different
 * language to optimize our model and return the weights for use in php.
 * Mostly common use: Optimize in a fast compiled language (ex.: C) or
 * a language with great libraries (ex.: Matlab)
 *
 * Output a json array that contains all the necessary information to
 * train the model and determine the weights that maximize the
 * conditional log likelihood of the training data
 *
 * The array has one element for each training document. The element is
 * a map with each possible class as key and an array of features that
 * fire for each class. There is a special key called '__label__' that
 * contains a string value which is the actual class of this document
 *
 * Ex.:
 * [
 *   {
 *     "class1": ["feature1","feature2","feature3"],
 *     "class2": ["feature1","feature4","feature5"],
 *     "__label__": "class1"
 *   },
 *   {
 *     "class1": ["feature2","feature3"],
 *     "class2": ["feature1","feature4","feature5"],
 *     "__label__": "class1"
 *   },
 *   {
 *     "class1": ["feature1","feature2","feature3"],
 *     "class2": ["feature1"],
 *     "__label__": "class2"
 *   }
 * ]
 *
 * Send this array to an external program that will return a map of
 * floats in json that will contain the weight for each feature.
 *
 */
class ExternalMaxentOptimizer implements MaxentOptimizerInterface
{
    // holds the program name to be run
    protected $optimizer;

    /**
     * @param string $optimizer The path for an external optimizer executable
     */
    public function __construct($optimizer)
    {
        $this->optimizer = $optimizer;
    }

    /**
     * Open a pipe to the optimizer, send him the data encoded in json
     * and then read the stdout to get the results encoded in json
     *
     * @param  array $feature_array The features that fired for any document for any class @see NlpTools\Models\Maxent
     * @return array The optimized weights
     */
    public function optimize(array &$feature_array)
    {
        // whete we will read from where we will write to
        $desrciptorspec = array(
            0=>array('pipe','r'),
            1=>array('pipe','w'),
            2=>STDERR // Should that be redirected to /dev/null or like?
        );

        // Run the optimizer
        $process = proc_open($this->optimizer,$desrciptorspec,$pipes);
        if (!is_resource($process)) {
            return array();
        }

        // send the data
        fwrite($pipes[0],json_encode($feature_array));
        fclose($pipes[0]);

        // get the weights
        $json = stream_get_contents($pipes[1]);

        // decode as an associative array
        $l = json_decode( $json , true );

        // close up the optimizer
        fclose($pipes[1]);
        proc_close($process);

        return $l;
    }

}
