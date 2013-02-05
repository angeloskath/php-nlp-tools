<?php

namespace NlpTools\Similarity;

/*
 * http://en.wikipedia.org/wiki/Jaccard_index
 * */
class JaccardIndex implements SetSimilarity, SetDistance
{
	/*
	 * The similarity returned by this algorithm is a number between 0,1
	 * */
	public function similarity(array &$setA, array &$setB) {
		$a = array_fill_keys($setA,1);
		$b = array_fill_keys($setB,1);
		
		$intersect = count(array_intersect_key($a,$b));
		$union = count(array_fill_keys(array_merge($setA,$setB),1));
		
		return $intersect/$union;
	}
	
	/*
	 * Jaccard Distance is simply the complement of the jaccard similarity
	 * */
	public function dist(array &$setA, array &$setB) {
		return 1-$this->similarity($setA,$setB);
	}
	
}

?>
