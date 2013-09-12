<?php

namespace NlpTools\Clustering\CentroidFactories;

/**
 * Computes the euclidean centroid of the provided sparse vectors
 */
class Euclidean implements CentroidFactoryInterface
{
    /**
     * If the document is a collection of tokens or features transorm it to
     * a sparse vector with frequency information.
     *
     * Ex.: If 'A' appears twice in the doc the dimension 'A' will have value 2
     * in the resulting vector
     *
     * @param  array $doc The doc data to transform to sparse vector
     * @return array A sparse vector representing the document to the n-dimensional euclidean space
     */
    protected function getVector(array $doc)
    {
        if (is_int(key($doc)))
            return array_count_values($doc);
        else
            return $doc;
    }

    /**
     * Compute the mean value for each dimension.
     *
     * @param  array $docs   The docs from which the centroid will be computed
     * @param  array $choose The indexes from which the centroid will be computed (if empty all the docs will be used)
     * @return mixed The centroid. It could be any form of data a number, a vector (it will be the same as the data provided in docs)
     */
    public function getCentroid(array &$docs, array $choose=array())
    {
        $v = array();
        if (empty($choose))
            $choose = range(0,count($docs)-1);
        $cnt = count($choose);
        foreach ($choose as $idx) {
            $doc = $this->getVector($docs[$idx]);
            foreach ($doc as $k=>$w) {
                if (!isset($v[$k]))
                    $v[$k] = $w;
                else
                    $v[$k] += $w;
            }
        }
        foreach ($v as &$w) {
            $w /= $cnt;
        }

        return $v;
    }
}
