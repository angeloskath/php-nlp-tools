<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Similarity\HammingDistance;

$A = "ABCDE";
$B = "FGHIJ";
$C = "10101";
$D = "11111";

$d = new HammingDistance();

_assert( $d->dist($A,$B)==max(strlen($A),strlen($B)) , "Two completely dissimilar strings should have distance equal to max(strlen(\$A),strlen(\$B))");

_assert( $d->dist($C,$D)==2 , "10101 ~ 11111 has hamming distance 2");


