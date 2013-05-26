<?php

namespace AssociativeHeap;

/**
 * Heap implements an associative heap whose elements can be accessed
 * as an array but inserting, changing value, deleting are of complexity O(logn).
 * Finding the "smallest" element of the array is of complexity O(1).
 * 
 * The Heap is implemented by a binary tree in array. Parent(i) = i/2, Left(i) = 2*i,
 * Right(i) = 2*i + 1
 */
abstract class Heap implements \Countable, \ArrayAccess, \Iterator
{
	// Iteration flags
	const ORDERED = 1;
	const UNORDERED = 0;

	// this holds the actual data
	protected $container;
	// this is the heap that maps to the keys of the container
	protected $heap;
	// this maps from the keys of the container to the keys of the heap
	protected $reverseContainer;
	// simply how many elements are in the Heap (should always be equal to count($container))
	protected $cnt;

	// Boolean flag that decides iteration behaviour
	protected $ordered_iter;

	/**
	 * Compare the two items and return positive,zero or negative if item1
	 * is bigger, equal to or smaller than item2 .
	 *
	 * @return integer
	 */
	abstract public function compare($item1,$item2);

	public function __construct() {
		$this->container = array();
		$this->heap = array();
		$this->reverseContainer = array();
		$this->cnt = 0;
		$this->ordered_iter = true;
	}

	/**
	 * Swap two elements in the heap while doing all the book-keeping.
	 */
	protected function swap($p,$c) {
		$t = $this->heap[$p];
		$this->heap[$p] = $this->heap[$c];
		$this->heap[$c] = $t;
		$this->reverseContainer[$this->heap[$p]] = $p;
		$this->reverseContainer[$this->heap[$c]] = $c;
	}
	// simply return the parent of $c in the heap
	protected function parent($c) {
		return (int)($c/2);
	}
	// return the left child of $p in the heap
	protected function child($p) {
		return 2*$p;	
	}
	/**
	 * Insert a new element in the heap or change the value of an existing element
	 * If the element is new append it to the end of the heap and make sure the
	 * heap property applies swapping upwards. If the element was already there
	 * just make sure the heap property applies trying to swap both upwards and downwards.
	 */
	protected function heapInsert($offset) {
		if (isset($this->reverseContainer[$offset])) // already exists
		{
			$i = $this->reverseContainer[$offset];
			$this->heapifyUp($i);   // swap upwards if needed
			$this->heapifyDown($i); // swap downwards if needed
		}
		else // new element
		{
			$this->heap[$this->cnt] = $offset; // append
			$this->reverseContainer[$offset] = $this->cnt;
			$this->heapifyUp($this->cnt); // swap upwards
			$this->cnt++;
		}
	}
	/**
	 * Ensure the heap property applies from this node to its children.
	 * Check if it does and swap downwards if it doesn't.
	 */
	protected function heapifyDown($i) {
		$c = $this->child($i);
		while ($c < $this->cnt) // while $i has still children
		{
			if (
				$c+1 < $this->cnt
				&& $this->compare(
					$this->container[$this->heap[$c]],
					$this->container[$this->heap[$c+1]]
				) > 0
			) // if left child is larger than the right
			{
				$c++;
			}
			if (
				$this->compare(
					$this->container[$this->heap[$i]],
					$this->container[$this->heap[$c]]
				) > 0
			) // if the parent is larger than the child swap
			{
				$this->swap($c,$i);
				$i = $c;
				$c = $this->child($i);
			}
			else // else stop we 're done
			{
				break;
			}
		}
	}

	/**
	 * Ensure the heap property applies from this node and upwards to its parents.
	 */
	protected function heapifyUp($i) {
		while ($i > 0) // while $i has parents
		{
			$p = $this->parent($i);
			if (
				$this->compare(
					$this->container[$this->heap[$p]],
					$this->container[$this->heap[$i]]
				) > 0
			) // if parent larger than child swap
			{
				$this->swap($p,$i);
				$i = $p;
			}
			else // stop, we 're done
			{
				break;
			}
		}
	}

	/**
	 * Remove node $i from the heap and make sure the heap property still applies.
	 */
	protected function heapRemove($i) {
		$this->cnt --;
		if ($i != $this->cnt)
			$this->swap($this->cnt, $i); // 1. exchange $i with the last element

		unset($this->reverseContainer[$this->heap[$this->cnt]]); // 2. remove the last element
		unset($this->heap[$this->cnt]);

		if ($i == $this->cnt)
			return;

		$this->heapifyUp($i);   // 3. Reheapify for the inserted element
		$this->heapifyDown($i);
	}

	/**
	 * Implements the ArrayAccess interface
	 * Assign $value to the key $offset. It could be either a new key or an existing one.
	 * Complexity: O(logn)
	 *
	 * @param mixed $offset A valid array key
	 * @param mixed $value Any value to associate with that key
	 */
	public function offsetSet($offset,$value) {
		$this->container[$offset] = $value;
		$this->heapInsert($offset);
	}

	/**
	 * Implements the ArrayAccess interface
	 * Complexity: O(1)
	 * 
	 * @param mixed $offset A valid array key
	 * @return mixed The value for the key $offset
	 */
	public function offsetGet($offset) {
		return $this->container[$offset];
	}

	/**
	 * Implements the ArrayAccess interface
	 * Wether the key exists or not.
	 * Complexity: O(1)
	 *
	 * @param mixed $offset An array key to test for existence
	 * @return bool Wether the key exists or not
	 */
	public function offsetExists($offset) {
		return isset($this->container[$offset]);
	}

	/**
	 * Implements the ArrayAccess interface
	 * Remove a key from the heap.
	 * Complexity: O(logn)
	 *
	 * @param mixed $offset The key to be removed
	 */
	public function offsetUnset($offset) {
		unset($this->container[$offset]);
		$this->heapRemove($this->reverseContainer[$offset]);
	}

	/**
	 * Implements Countable interface
	 * @return integer The number of elements in the Heap
	 */
	public function count() {
		return $this->cnt;
	}

	/**
	 * Remove and return the top of the heap
	 * @return array An array containing the key and the value of the top of the heap
	 */
	public function extract() {
		$key = $this->heap[0];
		$t = $this->container[$key];
		unset($this->container[$key]);
		$this->heapRemove(0);
		return array($key,$t);
	}

	/**
	 * Return the top of the heap without removing it
	 * @return array An array containing the key and the value of the top of the heap
	 */
	public function top() {
		$key = $this->heap[0];
		return array($key,$this->container[$key]);
	}

	/**
	 * Determine the iterator behaviour of the heap. Default is the in order extraction
	 * of the contents while emptying the heap. It can be selected to have an unordered
	 * iteration leaving the heap unchanged.
	 *
	 * @param integer $flags One of the constants ::ORDERED, ::UNORDERED
	 */
	public function setIteratorFlags($flags) {
		$this->ordered_iter = (bool)($flags & self::ORDERED);
	}

	/**
	 * Implements Iterator
	 * Depending on the IteratorFlags, either return the value corresponding to the key at the
	 * top of the heap or return the current value of the container array.
	 * @return mixed The current value of the iteration
	 */
	public function current() {
		$v = null;
		if ($this->ordered_iter)
			list($_,$v) = $this->top();
		else
			$v = current($this->container);
		return $v;
	}
	/**
	 * Implements Iterator
	 * Depending on the same rules as curent() return the corresponding key.
	 * @return mixed The current key of the iteration
	 */
	public function key() {
		$k = null;
		if ($this->ordered_iter)
			list($k,$_) = $this->top();
		else
			$k = key($this->container);
		return $k;
	}
	/**
	 * Implements Iterator
	 * If we are iterating in sorted order, remove the "smallest" element from the heap,
	 * else simply advance the internal pointer of the container array.
	 */
	public function next() {
		if ($this->ordered_iter)
			$this->extract();
		else
			next($this->container);
	}
	/**
	 * Implements Iterator
	 * If we are iterating in sorted order then the elements passed have been removed and
	 * cannot be retrieved again. So simply reset the internal pointer of the container
	 * array for the other occasion.
	 */
	public function rewind() {
		reset($this->container);
	}
	/**
	 * Implements Iterator
	 * The iterator is valid only if there still are items in the heap.
	 */
	public function valid() {
		return $this->cnt > 0;
	}
}

