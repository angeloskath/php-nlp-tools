<?php

namespace NlpTools\Similarity;

/**
 * https://en.wikipedia.org/wiki/Jaro%E2%80%93Winkler_distance
 * This class implements the Jaro distance of two strings or sets.
 * This accepts strings of arbitrary lengths.The score is normalized such that 0 equates to no similarity and 1 is 
 * an exact match.
 * 
 */

class JaroDistance implements DistanceInterface
{
    /**
     * Count the number of positions that A and B differ.
     *
     * @param  string $A
     * @param  string $B
     * @return float    The Jaro distance of the two strings A and B
     */
    public function dist(&$A, &$B)
    {
          $str1_len = strlen($A);
          $str2_len = strlen($B);
            
          $distance = (int) floor(min( $str1_len, $str2_len ) / 2.0); 
            
          $commons1 = $this->getCommonCharacters($A, $B, $distance );
          $commons2 = $this->getCommonCharacters($B, $A, $distance );
            
          if( ($commons1_len = strlen( $commons1 )) == 0) return 0;
          if( ($commons2_len = strlen( $commons2 )) == 0) return 0;
          $transpositions = 0;
          $upperBound = min( $commons1_len, $commons2_len );
          for( $i = 0; $i < $upperBound; $i++){
            if( $commons1[$i] != $commons2[$i] ) $transpositions++;
          }
          $transpositions /= 2.0;

          return ($commons1_len/($str1_len) + $commons2_len/($str2_len) + ($commons1_len - $transpositions)/($commons1_len)) / 3.0;
    }

    private function getCommonCharacters($string1, $string2, $allowedDistance){
  
      $str1_len = strlen($string1);
      $str2_len = strlen($string2);
      $temp_string2 = $string2;
       
      $commonCharacters='';
      for( $i=0; $i < $str1_len; $i++){
        
        $noMatch = true;
        for( $j= max( 0, $i-$allowedDistance ); $noMatch && $j < min( $i + $allowedDistance + 1, $str2_len ); $j++){
          if( $temp_string2[$j] == $string1[$i] ){
            $noMatch = false;
        $commonCharacters .= $string1[$i];
        $temp_string2[$j] = '';
          }
        }
      }
      return $commonCharacters;
    }
}