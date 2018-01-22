<?php

namespace NlpTools\Analysis;

use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\FeatureFactories\DataAsFeatures;

/**
 * Idf implements global collection statistics for use in different ranking schemes (DFR,VSM, etc.).
 * numberofTokens is the number of all tokens in the entire collection.
 * numberofDocuments is the number of documents in the collection.
 * termFrequency is the number of occurences of the word in the entire collection.
 * documentFrequency is the number of documents containing the word in the entire collection.
 *
 */

class Idf
{
    protected $numberofTokens;
    protected $numberofDocuments;
    protected $termFrequency;
    protected $documentFrequency;


    /**
     * @param TrainingSet $tset The set of documents for which we will compute the stats ()
     * @param FeatureFactoryInterface $ff   A feature factory to translate the document data to 
     * single tokens
     */
    public function __construct(TrainingSet $tset, FeatureFactoryInterface $ff=null)
    {
        if ($ff===null)
            $ff = new DataAsFeatures();

        $tset->setAsKey(TrainingSet::CLASS_AS_KEY);
        $this->numberofTokens = 0;
        $this->numberofDocuments = 0;
        foreach ($tset as $class=>$doc) {
            $this->numberofDocuments++;
            $tokens = $ff->getFeatureArray($class,$doc);
            $flag = array();
            foreach ($tokens as $term) {
                    $this->numberofTokens++;
                    $flag[$term] = isset($flag[$term]) && $flag[$term] === true ? true : false;

                    if (isset($this->termFrequency[$term])){
                        $this->termFrequency[$term]++;
                    } else {
                        $this->termFrequency[$term] = 1;
                    }

                    if (isset($this->documentFrequency[$term])){
                        if ($flag[$term] === false){
                            $flag[$term] = true;
                            $this->documentFrequency[$term]++;
                        }
                    } else {
                        $flag[$term] = true;
                        $this->documentFrequency[$term] = 1;
                    }
            }
        }

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
     * Returns number of documents in the collection.
     * 
     * @return mixed
     */
    public function numberofDocuments()
    {

        return $this->numberofDocuments;

    }

    /**
     * Returns number of occurences of the word in the entire collection.
     * 
     * @param  string $term
     * @return int
     */
    public function termFrequency($term)
    {

        if (isset($this->termFrequency[$term])) {
            return $this->termFrequency[$term];
        } else {
            return 0;
        }
    }

    /**
     * Returns number of documents containing the word in the entire collection.
     * 
     * @param  string $term
     * @return int
     */
    public function documentFrequency($term)
    {

        if (isset($this->documentFrequency[$term])) {
            return $this->documentFrequency[$term];
        } else {
            return 0;
        }
    }

    /**
     * Returns number of all tokens in the entire collection.
     * 
     * @return int
     */
    public function numberofCollectionTokens()
    {

        return $this->numberofTokens;
    }


}
