<?php

namespace NlpTools\Ranking;

use NlpTools\Documents\TrainingSet;
use NlpTools\Ranking\VectorScoringInterface;
use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\PivotTfIdfFeatureFactory;
use NlpTools\Analysis\PivotIdf;
use NlpTools\Math\Math;


/**
 * PivotedNormalizedVSM is using a pivoted normalized idf.
 *
 * Amit Singhal, John Choi, Donald Hindle, David Lewis, and Fernando Pereira. AT&T at TREC-7. In
 * Proceedings of the Seventh Text REtrieval Conference (TREC-7), pages 239–252. NIST Special Publication
 * 500-242, July 1999.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class PivotedNormalizedVSM extends AbstractRanking
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
        $this->stats = new PivotIdf($this->tset);
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

        $this->tfidf = new PivotTfIdfFeatureFactory(
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