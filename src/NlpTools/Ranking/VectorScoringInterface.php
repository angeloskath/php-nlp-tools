<?php

namespace NlpTools\Ranking;


interface VectorScoringInterface
{

    public function score($query, $documents);

}
