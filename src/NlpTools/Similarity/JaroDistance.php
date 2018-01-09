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
     * @param  string|array $A
     * @param  string|array $B
     * @return float    The Jaro distance of the two strings A and B
     */
    public function dist(&$A, &$B)
    {

         if(is_array($A) && is_array($B)) {
             $str1_len = count($A);
             $str2_len = count($B);
         }
         elseif(is_string($A) && is_string($B)) {
             $str1_len = strlen($A);
             $str2_len = strlen($B);
         }
         else {
            throw new \InvalidArgumentException(
                 "JaroDistance accepts only strings or arrays, not mixed"
            );
         }

        $distance = (int) floor(min( $str1_len, $str2_len ) / 2.0); 

        $commons1 = $this->getCommonCharacters($A, $B, $distance );
        $commons2 = $this->getCommonCharacters($B, $A, $distance );

        if(is_array($A) && is_array($B)) {
            if( ($commons1_len = count( $commons1 )) == 0) return 0;
            if( ($commons2_len = count( $commons2 )) == 0) return 0;
        }
        elseif(is_string($A) && is_string($B)) {
            if( ($commons1_len = strlen( $commons1 )) == 0) return 0;
            if( ($commons2_len = strlen( $commons2 )) == 0) return 0;
        }

        $transpositions = 0;
        $upperBound = min( $commons1_len, $commons2_len );
        for( $i = 0; $i < $upperBound; $i++){
        if( $commons1[$i] != $commons2[$i] ) $transpositions++;
        }
        $transpositions /= 2.0;

        return ($commons1_len/($str1_len) + $commons2_len/($str2_len) + ($commons1_len - $transpositions)/($commons1_len)) / 3.0;


    }

    private function getCommonCharacters($string1, $string2, $allowedDistance)
    {

        if(is_array($string1) && is_array($string2)) {
             $str1_len = count($string1);
             $str2_len = count($string2);
         }
         elseif(is_string($string1) && is_string($string2)) {
             $str1_len = strlen($string1);
             $str2_len = strlen($string2);
         }

          $temp_string2 = $string2;
           
          $commonCharacters= (is_string($string1) && is_string($string2)) ? '' : array();

          for( $i=0; $i < $str1_len; $i++){
            
            $noMatch = true;
            for( $j= max( 0, $i-$allowedDistance ); $noMatch && $j < min( $i + $allowedDistance + 1, $str2_len ); $j++){
              if( $temp_string2[$j] == $string1[$i] ){
                $noMatch = false;
                if (is_array($string1) && is_array($string2)) {
                    array_push($commonCharacters,$string1[$i]);
                }
                elseif(is_string($string1) && is_string($string2)) {
                    $commonCharacters .= $string1[$i];
                }
            $temp_string2[$j] = '';
              }
            }
          }
          return $commonCharacters;
    }


}