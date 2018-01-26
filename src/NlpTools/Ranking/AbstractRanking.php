<?php

namespace NlpTools\Ranking;

use NlpTools\Analysis\Idf;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\DocumentInterface;

abstract class AbstractRanking
{

    protected $tset;

    protected $stats;


    public function __construct(TrainingSet $tset)
    {
        $this->tset = $tset;
        if(count($this->tset) === 0){
           throw new \InvalidArgumentException(
                 "There are no Documents added."
            ); 
        }

        $this->stats = new Idf($this->tset);
        
    }

    /**
     * Returns the frequency of each terms in the query.
     *
     * @param  string $term
     * @param  array $query
     * @return int
     */
    protected function keyFrequency(array $query, $term) {
        $this->keyValues = array_count_values($query);
        if(array_key_exists($term, $this->keyValues)) {
            return $this->keyValues[$term];
        } else {
            return 0;
        }
    }


    abstract protected function search(DocumentInterface $q);

}