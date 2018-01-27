<?php

namespace NlpTools\Analysis;

use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\FeatureFactories\DataAsFeatures;

/**
 * tf is the number of occurences of the $term in a document with a known $key.
 * idf is the inverse function of the number of documents in which it occurs.
 */

class Idf extends Statistics
{

    protected $tf;

    /**
     * @param TrainingSet $tset The set of documents for which we will compute token stats
     * @param FeatureFactoryInterface $ff A feature factory to translate the document data to 
     * single tokens
     */
    public function __construct(TrainingSet $tset, FeatureFactoryInterface $ff=null)
    {
        parent::__construct($tset, $ff);
    }

    /**
     * Returns the idf weight containing the query word in the entire collection.
     * 
     * @param  string $term
     * @return mixed
     */
    public function idf($term)
    {

        if (isset($this->documentFrequency[$term])) {
            return log($this->numberofDocuments/$this->documentFrequency[$term]);
        } else {
            return log($this->numberofDocuments);
        }

    }

    /**
     * Returns number of occurences of the $term in a document with a known $key.
     * (tf)
     * While FreqDist Class is originally implemented as a one-off use to get tf from a collection of 
     * tokens, this should be used to get tf in relation to the entire corpus collection. Using this in 
     * Ranking should reduce reindexing time.
     *
     * @param  string $term
     * @param  int $key
     * @return int
     */
    public function tf($key, $term)
    {
        if (isset($this->tf[$key][$term])) {
            return $this->tf[$key][$term];
        } else {
            return 0;
        }
    }


}