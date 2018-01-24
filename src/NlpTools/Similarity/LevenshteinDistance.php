<?php

namespace NlpTools\Similarity;

/**
 * This class implements the Levenshtein distance of two strings or sets.
 * This accepts 2 strings of arbitrary lengths.
 * 
 */
class LevenshteinDistance implements DistanceInterface
{
    /**
     * Count the number of positions that A and B differ.
     *
     * @param  string $A
     * @param  string $B
     * @return int    The Levenshtein distance of the two strings A and B
     */
    public function dist(&$A, &$B)
    {
        $m = strlen($A);
        $n = strlen($B);
        
        for($i=0;$i<=$m;$i++) $d[$i][0] = $i;
        for($i=0;$i<=$n;$i++) $d[0][$i] = $i;
        
        for($i=1;$i<=$m;$i++) {
            for($j=1;$j<=$n;$j++) {
                $d[$i][$j] = $A[$i - 1] === $B[$j - 1] ? $d[$i - 1][$j - 1] : min($d[$i-1][$j]+1,
                                                                                  $d[$i][$j-1]+1,
                                                                                  $d[$i-1][$j-1]+1
                                                                                 );
            }
        }

        return $d[$m][$n];
    }
}
