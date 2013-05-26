<?php

include('../AssociativeHeap/Heap.php');
include('../AssociativeHeap/MinHeap.php');

$h = new AssociativeHeap\MinHeap();

$h[0] = 9;
$h[1] = 8;
$h[2] = 7;
$h[3] = 6;
$h[4] = 5;
$h[5] = 4;
$h[6] = 3;
$h[7] = 2;

unset($h[4]);
$h[4] = 10;
$h[5] = 0;

if (count($h) != 8)
	throw new Exception("The heap should contain 8 elements");

foreach ($h as $k=>$v)
{
	echo $k,"=>",$v,PHP_EOL;
}
