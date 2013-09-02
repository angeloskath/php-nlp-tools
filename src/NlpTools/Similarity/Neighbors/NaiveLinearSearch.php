<?php

namespace NlpTools\Similarity\Neighbors;

use NlpTools\Similarity\Distance;

/**
 * Search a set of documents one by one and recompute the distances in order
 * to answer to spatial queries.
 */
class NaiveLinearSearch implements SpatialIndexInterface
{
    protected $dist;
    protected $docs_copy;

    /**
     * Update the distance metric with which the distances are computed
     *
     * @param Distance $d The distance metric
     */
    public function setDistanceMetric(Distance $d)
    {
        $this->dist = $d;
    }

    /**
     * Process the documents for faster querying. This implementation does
     * not do any preprocessing.
     *
     * @param array $docs The array of documents to process
     */
    public function index(array &$docs)
    {
        $this->docs_copy = &$docs;
    }

    /**
     * Add another document to the index
     *
     * @param mixed $doc The document to be added to the index
     */
    public function add($doc)
    {
        $this->docs_copy[] = $doc;
    }

    /**
     * Search all the documents and return those that are within
     * $e distance of $doc
     *
     * @param  mixed $doc The document in whose region we are searching
     * @param  float $e   The maximum allowed distance from $doc
     * @return array The indexes of the documents that are the answer to the query
     */
    public function regionQuery($doc, $e)
    {
        $idxs = array();
        foreach ($this->docs_copy as $idx=>$d) {
            if ($this->dist->dist($d,$doc) < $e) {
                $idxs[] = $idx;
            }
        }

        return $idxs;
    }

    /**
     * Search all the documents and return the k closest to $doc
     *
     * @param  mixed $doc The document whose neighbors we need to return
     * @param  int   $k   How many neighbors to return
     * @return array The indexes of the k closest neighbors
     */
    public function kNearestNeighbors($doc, $k)
    {
        // We keep a sorted array with the k nearest neighbors while
        // traversing through all the documents
        $neighbors = array_fill_keys(
            range(0,$k-1),
            array(-1,INF)
        );
        $last = $k-1;
        foreach ($this->docs_copy as $idx=>$d) {
            $dist = $this->dist->dist($doc, $d);
            if ($dist < $neighbors[$last][1]) {
                $neighbors[$last][1] = $dist;
                $neighbors[$last][0] = $idx;
                for ($j=$last-1;$j>0;$j++) {
                    if ($neighbors[$j][1] < $neighbors[$j+1][1]) {
                        break;
                    }
                    $tmp = $neighbors[$j];
                    $neighbors[$j] = $neighbors[$j+1];
                    $neighbors[$j+1] = $tmp;
                }
            }
        }

        return array_map(
            function ($n) {
                return $n[0];
            },
            $neighbors
        );
    }
}
