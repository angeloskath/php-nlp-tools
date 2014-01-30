<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\DistanceInterface;

/**
 * HeapLinkage is an abstract merge strategy.
 *
 * It creates a pairwise distance matrix. It then uses a heap to compute
 * efficiently the minimum in the distance matrix. Then the two clusters
 * with the minimum distance are merged and the distance of the new cluster
 * with every other cluster i is recomputed. This recomputation is delegated
 * to the children classes through the abstract function newDistance().
 *
 * The class uses an SplFixedArray that is filled with the lower triangle of
 * the distance matrix. This is done to save memory. The index of a pair x,y
 * is computed as follows:
 *  1. if x>y swap x,y
 *  2. index = y*(y-1)/2 + x
 */
abstract class HeapLinkage implements MergeStrategyInterface
{
    protected $L;
    protected $queue;
    protected $dm;
    protected $removed;

    /**
     * Calculate the distance of the merged cluster x,y with cluster i
     * based on a merge strategy (SingleLink, CompleteLink, GroupAverage, ...)
     * Ex.: for single link this function would be
     * return min($this->dm[$xi],$this->dm[$yi]);
     */
    abstract protected function newDistance($xi,$yi,$x,$y);

    /**
     * Initialize the distance matrix and any other data structure needed
     * to calculate the merges later.
     *
     * @param DistanceInterface $d    The distance metric used to calculate the distance matrix
     * @param array             $docs The docs to be clustered
     */
    public function initializeStrategy(DistanceInterface $d, array &$docs)
    {
        // the number of documents and the dimensions of the matrix
        $this->L = count($docs);
        // just to hold which document has been removed
        $this->removed = array_fill_keys(range(0, $this->L-1), false);
        // how many distances we must compute
        $elements = (int) ($this->L*($this->L-1))/2;
        // the containers that will hold the distances
        $this->dm = new \SplFixedArray($elements);
        $this->queue = new \SplPriorityQueue();
        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);

        // for each unique pair of documents calculate the distance and
        // save it in the heap and distance matrix
        for ($x=0;$x<$this->L;$x++) {
            for ($y=$x+1;$y<$this->L;$y++) {
                $index = $this->packIndex($y,$x);
                $tmp_d = $d->dist($docs[$x],$docs[$y]);
                $this->dm[$index] = $tmp_d;
                $this->queue->insert($index, -$tmp_d);
            }
        }
    }

    /**
     * Return the pair of clusters x,y to be merged.
     *  1. Extract the pair with the smallest distance
     *  2. Recalculate the distance of the merged cluster with every other cluster
     *  3. Merge the clusters (by labeling one as removed)
     *  4. Reheap
     *
     * @return array The pair (x,y) to be merged
     */
    public function getNextMerge()
    {
        // extract the pair with the smallest distance
        $tmp = $this->queue->extract();
        $index = $tmp["data"];
        $d = -$tmp["priority"];
        list($y,$x) = $this->unravelIndex($index);
        // check if it is invalid
        while ($this->removed[$y] || $this->removed[$x] || $this->dm[$index]!=$d) {
            $tmp = $this->queue->extract();
            $index = $tmp["data"];
            $d = -$tmp["priority"];
            list($y,$x) = $this->unravelIndex($index);
        }

        // Now that we have a valid pair to be merged
        // calculate the distances of the merged cluster with any
        // other cluster
        $yi = $this->packIndex($y,0);
        $xi = $this->packIndex($x,0);

        // for every cluster with index i<x<y
        for ($i=0;$i<$x;$i++,$yi++,$xi++) {
            $d = $this->newDistance($xi,$yi,$x,$y);
            if ($d!=$this->dm[$xi]) {
                $this->dm[$xi] = $d;
                $this->queue->insert($xi, -$d);
            }
        }
        // for every cluster with index x<i<y
        for ($i=$x+1;$i<$y;$i++,$yi++) {
            $xi = $this->packIndex($i,$x);
            $d = $this->newDistance($xi,$yi,$x,$y);
            if ($d!=$this->dm[$xi]) {
                $this->dm[$xi] = $d;
                $this->queue->insert($xi, -$d);
            }
        }
        // for every cluster x<y<i
        for ($i=$y+1;$i<$this->L;$i++) {
            $xi = $this->packIndex($i,$x);
            $yi = $this->packIndex($i,$y);
            $d = $this->newDistance($xi,$yi,$x,$y);
            if ($d!=$this->dm[$xi]) {
                $this->dm[$xi] = $d;
                $this->queue->insert($xi, -$d);
            }
        }

        // mark y as removed
        $this->removed[$y] = true;

        return array($x,$y);
    }

    /**
     * Use binary search to unravel the index to its coordinates x,y
     * return them in the order y,x . This operation is to be done only
     * once per merge so it doesn't add much overhead.
     *
     * Note: y will always be larger than x
     *
     * @param  integer $index The index to be unraveled
     * @return array   An array containing (y,x)
     */
    protected function unravelIndex($index)
    {
        $a = 0;
        $b = $this->L-1;
        $y = 0;
        while ($b-$a > 1) {
            // the middle row in the interval [a,b]
            $y = (int) (($a+$b)/2);
            // the candidate index aka how many points until this row
            $i = $y*($y-1)/2;

            // if we need an offset les then the wanted y will be in the offset [a,y]
            if ($i > $index) {
                $b = $y;
            } else {
            // else it will be in the offset [y,b]
                $a = $y;
            }
        }
        // we have finished searching it is either a or b
        $x = $index - $i;

        // this means that it is b and we have a
        if ($y <= $x) {
            $y++;
            $x = $index - $y*($y-1)/2;
        } elseif ($x < 0) {
        // this means that it is a and we have b
            $y--;
            $x = $index - $y*($y-1)/2;
        }

        return array(
            (int) $y,
            (int) $x
        );
    }

    /**
     * Pack the coordinates x and y to an integer offset from 0.
     * The first line (y=0) contains 0 elements, the 2nd 1 the 3rd 2 ...
     * So to calculate how many elements are from the first to the nth line
     * we should calculate the sum 0+1+2+...+n-1 which is equal to (n-1)*n / 2
     *
     * Note: y must always be larger than x
     *
     * @param  integer $y The y coordinate (large)
     * @param  integer $x The x coordinate (small)
     * @return integer The offset in the low triangle matri containing the item (x,y)
     */
    protected function packIndex($y, $x)
    {
        return $y*($y-1)/2 + $x;
    }
}
