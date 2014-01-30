<?php

namespace NlpTools\FeatureFactories;

use \NlpTools\Documents\DocumentInterface;

/**
 * An implementation of FeatureFactoryInterface that takes any number of callables
 * (function names, closures, array($object,'func_name'), etc.) and
 * calls them consecutively using the return value as a feature's unique
 * string.
 *
 * The class can model both feature frequency and presence
 */
class FunctionFeatures implements FeatureFactoryInterface
{

    protected $functions;
    protected $frequency;

    /**
     * @param array $f An array of feature functions
     */
    public function __construct(array $f=array())
    {
        $this->functions=$f;
        $this->frequency=false;
    }
    /**
     * Set the feature factory to model frequency instead of presence
     */
    public function modelFrequency()
    {
        $this->frequency = true;
    }
    /**
     * Set the feature factory to model presence instead of frequency
     */
    public function modelPresence()
    {
        $this->frequency = false;
    }
    /**
     * Add a function as a feature
     *
     * @param callable $feature
     */
    public function add( $feature )
    {
        $this->functions[] = $feature;
    }

    /**
     * Compute the features that "fire" for a given class,document pair.
     *
     * Call each function one by one. Eliminate each return value that
     * evaluates to false. If the return value is a string add it to
     * the feature set. If the return value is an array iterate over it
     * and add each value to the feature set.
     *
     * @param  string            $class The class for which we are calculating features
     * @param  DocumentInterface $d     The document for which we are calculating features
     * @return array
     */
    public function getFeatureArray($class, DocumentInterface $d)
    {
        $features = array_filter(
            array_map( function ($feature) use ($class,$d) {
                    return call_user_func($feature, $class, $d);
                },
                $this->functions
            ));
        $set = array();
        foreach ($features as $f) {
            if (is_array($f)) {
                foreach ($f as $ff) {
                    if (!isset($set[$ff]))
                        $set[$ff] = 0;
                    $set[$ff]++;
                }
            } else {
                if (!isset($set[$f]))
                    $set[$f] = 0;
                $set[$f]++;
            }
        }
        if ($this->frequency)
            return $set;
        else
            return array_keys($set);
    }

}
