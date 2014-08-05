<?php

namespace NlpTools\Similarity\Neighbors;

class KDTreeTest extends NeighborsTestAbstract
{
    protected function getSpatialIndexInstance()
    {
        return new KDTree();
    }
}
