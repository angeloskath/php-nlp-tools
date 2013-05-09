<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Similarity\CosineSimilarity;

$sim = new CosineSimilarity();

$A = array(1,2,3);
$A2 = array(1,2,3,1,2,3);
$B = array(1,2,3,4,5,6);

// triangle
// A = (2,4)
// B = (0,0)
// C = (3,2)
// 1 will be x and 2 will be y
$ba = array(1,1,2,2,2,2); // ba = (2,4)
$bc = array(1,1,1,2,2); // bc = (3,2)
$bba = array('0'=>2,'1'=>4);
$bbc = array('0'=>3,'1'=>2);
$ba_to_bc = cos(0.5191461142); // approximately 30 deg


// this is a rounding threshold because (A•A / |A|*|A|) != 1
$rt = 1e-10;

_assert(abs($sim->similarity($A,$A)-1)<$rt,"Cosine similarity of a set with itsself should be 1");
_assert(abs($sim->similarity($A,$A2)-1)<$rt,"Cosine similarity of a set with any linear combination of it should be 1");
_assert($sim->similarity($A,$B)-$sim->similarity($A2,$B) < $rt,"Parallel vectors should have the same angle with any vector B");

// Test the triangle
_assert($sim->similarity($ba,$bc)-$ba_to_bc < $rt,"CosineSim[{2,4},{3,2}]=0.8682431421244593 instead of {$sim->similarity($ba,$bc)}");

// Same as above just passing already made vectors
_assert($sim->similarity($bba,$bbc)-$ba_to_bc < $rt,"CosineSim[{2,4},{3,2}]=0.8682431421244593 instead of {$sim->similarity($bba,$bbc)}");

