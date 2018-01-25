<?php

namespace NlpTools\Ranking;

use NlpTools\Math\Math;
use NlpTools\Ranking\VectorScoringInterface;


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


class VectorSpaceModel implements VectorScoringInterface
{


    protected $math;

    public function __construct() 
    {
        $this->math = new Math();
    }

    /**
     * Returns Score ranking per Documents added by ascending order.
     *
     * @param  string $term
     * @return array
     */
    public function score($query, $documents)
    {

        $normA = $this->math->norm($query);
        $normB = $this->math->norm($documents);
        return (($normA * $normB) != 0)
               ? $this->math->dotProduct($query, $documents) / ($normA * $normB)
               : 0;

    }


}