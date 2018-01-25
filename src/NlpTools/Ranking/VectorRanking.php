<?php

namespace NlpTools\Ranking;

use NlpTools\Documents\TrainingSet;
use NlpTools\Ranking\VectorScoringInterface;
use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\TfIdfFeatureFactory;


/**
 * A Wrapper for weighted retrieval using a specific IR scheme
 *
 * The class receives an implementation of VectorScoringInterface, and TrainingSet, then tokenized 
 * queries to search and compute each TrainingSet document's score.
 */

class VectorRanking extends AbstractRanking
{


    protected $query;

    protected $score;

    protected $type;

    protected $tfidf;

    public function __construct(VectorScoringInterface $type, TrainingSet $tset)
    {
        parent::__construct($tset);
        $this->type = $type;
    }

    /**
     * Returns result ordered by rank.
     *
     * @param  DocumentInterface $q
     * @return array
     */
    public function search(DocumentInterface $q)
    {

        $this->tfidf = new TfIdfFeatureFactory(
            $this->stats,
            array(
                function ($c, $d) {
                    return $d->getDocumentData();
                }
            )
        );

        $this->query = $q;

        $this->score = array();

        for($i = 0; $i < count($this->tset); $i++){
            $query = $this->tfidf->getFeatureArray('', $this->query);
            $documents = $this->tfidf->getFeatureArray('', $this->tset->offsetGet($i));
            $this->score[$i] = $this->type->score($query, $documents);
        }

        arsort($this->score);
        return $this->score;

    }



}