<?php

namespace NlpTools\Clustering\CentroidFactories;

/**
This class computes the centroid of the hamming distance between two strings
that are the binary representations of two integers (the strings are supposed
to only contain the characters 1 and 0).
 */
class Hamming implements CentroidFactoryInterface
{

    /**
     * Return a number in binary encoding in a string such that the sum of its
     * hamming distances of each document is minimized.
     *
     * Assumptions: The docs array should contain strings that are properly padded
     * 			 binary (they should all be the same length).
     */
    public function getCentroid(array &$docs, array $choose=array())
    {
        $bitl = strlen($docs[0]);
        $buckets = array_fill_keys(
            range(0,$bitl-1),
            0
        );
        if (empty($choose))
            $choose = range(0,count($docs)-1);
        foreach ($choose as $idx) {
            $s = $docs[$idx];
            foreach ($buckets as $i=>&$v) {
                if ($s[$i]=='1')
                    $v += 1;
                else
                    $v -= 1;
            }
        }

        return implode(
            '',
            array_map(
                function ($v) {
                    return ($v>0) ? '1' : '0';
                },
                $buckets
            )
        );
    }

}
