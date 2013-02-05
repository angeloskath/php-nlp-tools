<?php

include ('../../../autoloader.php');

use NlpTools\Similarity\Simhash;

$sim = new Simhash(64);
$A = array(1,2,3);

$start = microtime(true);
for ($i=0;$i<1000;$i++)
{
	$sim->simhash($A);
}
echo microtime(true)-$start,PHP_EOL;

?>
