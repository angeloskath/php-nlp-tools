<?php

namespace NlpTools\Ranking;

use NlpTools\Documents\TrainingSet;
use NlpTools\Ranking\VectorScoringInterface;
use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\TfIdfFeatureFactory;
use NlpTools\Analysis\Idf;
use NlpTools\Math\Math;


/**
 * Vector Space Model is a Class for calculating Relevance ranking by comparing the deviation of angles
 * between each document vector and the original query vector.
 *
 * It uses Cosine Similarity as similarity measure between tfidf vector matrices.
 * You can use current implementation of cosine similarity but it was made to return an
 * Exception in case of 0 vector product instead of 0.
 *
 * https://en.wikipedia.org/wiki/Vector_space_model#Example:_tf-idf_weights
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class VectorSpaceModel extends AbstractRanking
{


    protected $query;

    protected $score;

    protected $type;

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