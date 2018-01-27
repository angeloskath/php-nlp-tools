<?php

namespace NlpTools\Ranking;

use NlpTools\Documents\TrainingSet;
use NlpTools\Ranking\VectorScoringInterface;
use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\LemurTfIdfFeatureFactory;
use NlpTools\Analysis\Idf;
use NlpTools\Math\Math;


/**
 * This class implements the TF_IDF weighting model as it is implemented in Lemur Project.
 * See http://www.cs.cmu.edu/~lemur/1.0/tfidf.ps Notes on the Lemur TFIDF model. Chenxiang Zhai, 2001</a>.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class LemurTfIdfVSM extends AbstractRanking
{


    protected $query;

    protected $score;

    protected $stats;

    protected $tfidf;

    protected $math;

    public function __construct(TrainingSet $tset)
    {
        parent::__construct($tset);
        $this->stats = new Idf($this->tset);
        $this->math = new Math();
    }

    /**
     * Returns result ordered by rank.
     *
     * @param  DocumentInterface $q
     * @return array
     */
    public function search(DocumentInterface $q)
    {

        $this->tfidf = new LemurTfIdfFeatureFactory(
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
            $this->score[$i] = $this->score($query, $documents);
        }

        arsort($this->score);
        return $this->score;

    }

    private function score($query, $documents)
    {

        $normA = $this->math->norm($query);
        $normB = $this->math->norm($documents);
        return (($normA * $normB) != 0)
               ? $this->math->dotProduct($query, $documents) / ($normA * $normB)
               : 0;

    }



}