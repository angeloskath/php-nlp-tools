<?php

include('../AssociativeHeap/Heap.php');
include('../AssociativeHeap/MinHeap.php');


$b = memory_get_usage(true);
$h = new AssociativeHeap\MinHeap();
$n = 100000;
for ($i=0;$i<$n;$i++) {
	$h[$i] = mt_rand()%$n;
}
$b = memory_get_usage(true) - $b;
echo $b,PHP_EOL;

$prev = -INF;
foreach ($h as $k=>$t)
{
	if ($prev > $t)
		throw new Exception("The elements should be ordered");
	$prev = $t;
}

if (count($h)>0)
	throw new Exception("The heap should be empty after iteration");
