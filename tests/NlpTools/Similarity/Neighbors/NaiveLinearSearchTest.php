<?php

namespace NlpTools\Similarity\Neighbors;

class NaiveLinearSearchTest extends NeighborsTestAbstract
{
    protected function getSpatialIndexInstance()
    {
        return new NaiveLinearSearch();
    }
}
