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


    abstract protected function search(DocumentInterface $q);

}