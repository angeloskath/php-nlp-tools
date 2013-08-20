<?php

namespace NlpTools\Utils;

/**
 * MatrixHeap wraps anything that implements the array access interface
 * and adds the heap functionality. One can insert keys and then use extract
 * to get them ordered by the values of the wrapped array.
 */
class MatrixHeap extends \SplMinHeap implements \ArrayAccess
{
	// the object that implements the ArrayAccess interface
	protected $matrix;

	public function __construct(&$matrix) {
		$this->matrix = $matrix;
	}
	/**
	 * Compare two keys based on the values in the matrix.
	 *
	 * @return int The difference between matrix[$v2] and matrix[$v2]
	 */
	public function compare($v1,$v2) {
		return $this->matrix[$v2]-$this->matrix[$v1];
	}

	// Wrap the array access interface
	public function offsetSet($offset,$v) {
		$this->matrix[$offset] = $v;
	}
	public function offsetGet($offset) {
		return $this->matrix[$offset];
	}
	public function offsetUnset($offset) {
		unset($this->matrix[$offset]);
	}
	public function offsetExists($offset) {
		return isset($this->matrix[$offset]);
	}
}
