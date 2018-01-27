<?php

namespace NlpTools\Similarity;

/**
 * https://en.wikipedia.org/wiki/Jaro%E2%80%93Winkler_distance
 * This class implements the JaroWinkler distance of two strings or sets.
 * This accepts strings of arbitrary lengths.The score is normalized such that 0 equates to no similarity and 1 is 
 * an exact match. Jaro Winkler distance built a logic on top of Jaro distance which added some weight if they have 
 * the same prefix (which has a maximum length of 4).
 * 
 */

class JaroWinklerDistance extends JaroDistance implements DistanceInterface
{

    /**
     * @param $prefix is a constant scaling factor for how much the score is adjusted upwards for having common 
     * prefixes. It should not exceed 0.25, otherwise the distance can become larger than 1. 
     * The standard value for this constant in Winkler's work is p=0.1
     */
    public function __construct($prefix=0.1)
    {
        $this->prefix = $prefix;
    }

    /**
     * Count the number of positions that A and B differ.
     *
     * @param  string $A
     * @param  string $B
     * @return float    The JaroWinkler distance of the two strings A and B
     */
    public function dist(&$A, &$B)
    {
          $distance = parent::dist($A, $B);
  
          $prefixLength = $this->getPrefixLength($A,$B);

          $prefix = $this->prefix;
          
          return $distance + $prefixLength * $prefix * (1.0 - $distance);
    }

    private function getPrefixLength($string1, $string2, $MINPREFIXLENGTH = 4)
    {

          if(is_array($string1) && is_array($string2)) {
               $str1_len = count($string1);
               $str2_len = count($string2);
           }
           elseif(is_string($string1) && is_string($string2)) {
               $str1_len = strlen($string1);
               $str2_len = strlen($string2);
           }

          $n = min(array($MINPREFIXLENGTH, $str1_len, $str2_len));
          
          for($i = 0; $i < $n; $i++){
            if( $string1[$i] != $string2[$i] ){
              return $i;
            }
          }

          return $n;
  }

}