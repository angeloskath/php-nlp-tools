<?php

namespace AssociativeHeap;

/**
 * MaxHeap is, as the name implies, a heap with the largest item on top.
 */
class MaxHeap extends Heap
{
	public function compare($item1,$item2) {
		return $item2-$item1;
	}
}

