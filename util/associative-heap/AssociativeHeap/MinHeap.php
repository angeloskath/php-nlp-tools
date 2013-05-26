<?php

namespace AssociativeHeap;

/**
 * MinHeap is the classic min heap implementation. The smallest element on the
 * top of the heap.
 */
class MinHeap extends Heap
{
	public function compare($item1,$item2) {
		return $item1-$item2;
	}
}

