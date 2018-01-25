<?php

namespace NlpTools\Ranking;

use NlpTools\Documents\TrainingSet;
use NlpTools\Ranking\ScoringInterface;
use NlpTools\Documents\DocumentInterface;


/**
 * A Wrapper for weighted retrieval using a specific IR scheme
 *
 * The class receives an implementation of ScoringInterface, and TrainingSet, then tokenized queries to 
 * search and compute each TrainingSet document's score.
 */

class Ranking extends AbstractRanking
{


    protected $query;

    protected $score;

    protected $type;

    protected $tf;

    public function __construct(ScoringInterface $type, TrainingSet $tset)
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

        $this->query = $q;

        $this->score = array();

        //∑(Document, Query)
        foreach ($this->query->getDocumentData() as $term){
            $documentFrequency = $this->stats->documentFrequency($term);
            $keyFrequency = $this->keyFrequency($this->query->getDocumentData(), $term);
            $termFrequency = $this->stats->termFrequency($term);
            $collectionLength = $this->stats->numberofCollectionTokens();
            $collectionCount = $this->stats->numberofDocuments();
            for($i = 0; $i < $collectionCount; $i++){
                $this->score[$i] = isset($this->score[$i]) ? $this->score[$i] : 0;
                $docLength = $this->stats->numberofDocumentTokens($i);
                $tf = $this->stats->tf($i, $term); 
                $this->score[$i] += $this->type->score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount);
            }
        }

        arsort($this->score);
        return $this->score;
    }

    /**
     * Returns the frequency of each terms in the query.
     *
     * @param  string $term
     * @param  array $query
     * @return int
     */
    private function keyFrequency(array $query, $term) {
        $this->keyValues = array_count_values($query);
        if(array_key_exists($term, $this->keyValues)) {
            return $this->keyValues[$term];
        } else {
            return 0;
        }
    }


}