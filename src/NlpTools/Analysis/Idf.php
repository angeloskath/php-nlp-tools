<?php

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
 */
class Idf implements \ArrayAccess
{
    protected $logD;
    protected $idf;

    /**
     * @param TrainingSet             $tset The set of documents for which we will compute the idf
     * @param FeatureFactoryInterface $ff   A feature factory to translate the document data to single tokens
     */
    public function __construct(TrainingSet $tset, FeatureFactoryInterface $ff=null)
    {
        if ($ff===null)
            $ff = new DataAsFeatures();

        $tset->setAsKey(TrainingSet::CLASS_AS_KEY);
        foreach ($tset as $class=>$doc) {
            $tokens = $ff->getFeatureArray($class,$doc); // extract tokens from the document
            $tokens = array_fill_keys($tokens,1); // make them occur once
            foreach ($tokens as $token=>$v) {
                if (isset($this->idf[$token]))
                    $this->idf[$token]++;
                else
                    $this->idf[$token] = 1;
            }
        }

        // this idf so far contains the doc frequency
        // we will now inverse it and take the log
        $D = count($tset);
        foreach ($this->idf as &$v) {
            $v = log($D/$v);
        }
        $this->logD = log($D);
    }

    /**
     * Implements the array access interface. Return the computed idf or
     * the logarithm of the count of the documents for a token we have not
     * seen before.
     *
     * @param  string $token The token to return the idf for
     * @return float  The idf
     */
    public function offsetGet($token)
    {
        if (isset($this->idf[$token])) {
            return $this->idf[$token];
        } else {
            return $this->logD;
        }
    }

    /**
     * Implements the array access interface. Return true if the token exists
     * in the corpus.
     *
     * @param  string $token The token to check if it exists in the corpus
     * @return bool
     */
    public function offsetExists($token)
    {
        return isset($this->idf[$token]);
    }

    /**
     * Will not be implemented. Throws \BadMethodCallException because
     * one should not be able to alter the idf values directly.
     */
    public function offsetSet($token, $value)
    {
        throw new \BadMethodCallException("The idf of a specific token cannot be set explicitly");
    }

    /**
     * Will not be implemented. Throws \BadMethodCallException because
     * one should not be able to alter the idf values directly.
     */
    public function offsetUnset($token)
    {
        throw new \BadMethodCallException("The idf of a specific token cannot be unset");
    }
}
