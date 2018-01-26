<?php

namespace NlpTools\Ranking;

use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\DocumentInterface;
use NlpTools\Ranking\BasicModel\BasicModelInterface;
use NlpTools\Ranking\AfterEffect\AfterEffectInterface;
use NlpTools\Ranking\Normalization\NormalizationInterface;


/**
 * DFRWeightingModel is a framework for ranking documents against a query based on Harter's 2-Poisson index-model.
 * This class provides an alternative way of specifying an arbitrary DFR weighting model, by mixing the used components.
 *
 * The implementation is strictly based on G. Amati, C. Rijsbergen paper:
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.97.8274&rep=rep1&type=pdf
 *
 * DFR models are obtained by instantiating the three components of the framework: 
 * selecting a basic randomness model, applying the first normalisation and normalising the term frequencies.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DFRRanking extends AbstractRanking
{

    protected $query;

    protected $score;

    protected $basicmodel;

    protected $aftereffect;

    protected $normalization;

    public function __construct(BasicModelInterface $basicmodel, AfterEffectInterface $aftereffect, NormalizationInterface $normalization, TrainingSet $tset)
    {
        parent::__construct($tset);
        $this->basicmodel    = $basicmodel;
        $this->aftereffect    = $aftereffect;
        $this->normalization    = $normalization;

        if ($this->basicmodel == null || $this->aftereffect == null || $this->normalization == null) {
            throw new \Exception("Null Parameters not allowed.");
        }

    }

    /**
     * Returns result ordered by rank.
     *
     * @param  DocumentInterface $q
     * @return array
     */
    public function search(DocumentInterface $q)
    {

        $this->query = $q;

        $this->score = array();

        //∑(Document, Query)
        foreach ($this->query->getDocumentData() as $term){
            $documentFrequency = $this->stats->documentFrequency($term);
            $termFrequency = $this->stats->termFrequency($term);
            $collectionLength = $this->stats->numberofCollectionTokens();
            $collectionCount = $this->stats->numberofDocuments();
            for($i = 0; $i < $collectionCount; $i++){
                $this->score[$i] = isset($this->score[$i]) ? $this->score[$i] : 0;
                $docLength = $this->stats->numberofDocumentTokens($i);
                $tf = $this->stats->tf($i, $term);
                if($tf != 0) {
                    $tfn = $tf;

                    if($this->normalization) {
                        $tfn = $this->normalization->normalise($tf, $docLength, $termFrequency, $collectionLength); 
                    }

                    $gain = 1;

                    if ($this->aftereffect) {
                        $gain = $this->aftereffect->gain($tfn, $documentFrequency, $termFrequency);
                    }

                    $this->score[$i] += $gain * $this->basicmodel->score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount);
                }
                
            }
        }

        arsort($this->score);
        return $this->score;

    }

}