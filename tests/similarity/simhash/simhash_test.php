<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Similarity\Simhash;

$sim = new Simhash(64); //md5 is used by default

$A = array(1,2,3);
$B = array(1,2,3,4,5,6);
$b = array(1,2,3,4,5);
$e = array();

_assert($sim->similarity($A,$A)==1, "A set with an identical set should have the same exact hash");
_assert($sim->dist($A,$B) > $sim->dist($b,$B), "Set A should be further than set b is from B since they have less common elements");



?>
