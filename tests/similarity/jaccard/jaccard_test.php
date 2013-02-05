<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Similarity\JaccardIndex;

$sim = new JaccardIndex();

$A = array(1,2,3);
$B = array(1,2,3,4,5,6);
$e = array();

_assert($sim->similarity($A,$A)==1,"Jaccard index of a set with itsself should be 1");
_assert($sim->similarity($A,$e)==0,"Jaccard index of a set with an empty set should be 0");
_assert($sim->similarity($A,$B)==0.5,"J({1,2,3},{1,2,3,4,5,6}) = 0.5");

?>
