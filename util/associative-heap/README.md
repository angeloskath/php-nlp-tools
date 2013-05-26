Associative Heap
----------------

An associative heap is a data structure that combines a heap and an array for easy access by key and also easy access of the min/max element in the array.

It has O(1) access time for each element by key and O(1) access time for the min/max element. It also has O(logn) insert/remove/update times.

As far as space is concerned, it wastes a bit of space by using three arrays to hold the data and the relationships between them. There is a container array that maps keys to values, there is an array for the heap that maps heap positions (the actual tree) to keys. In order to be able to remove arbitrary elements from the heap in O(logn) time we also keep an array that maps keys to their heap positions.

Example
-------

``` php
include('AssociativeHeap/Heap.php');
include('AssociativeHeap/MinHeap.php');

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

// in order iteration
foreach ($h as $k=>$v)
{
	echo $k,"=>",$v,PHP_EOL;
}

// Output
// ------
// 5=>0
// 7=>2
// 6=>3
// 3=>6
// 2=>7
// 1=>8
// 0=>9
// 4=>10
```

Purpose
-------

This class was implemented for use with [NlpTools](http://php-nlp-tools.com/) in reducing the hierarchical clustering complexity from O(n<sup>3</sup>) to O(n<sup>2</sup>logn).

