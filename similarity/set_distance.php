<?php

namespace NlpTools\Similarity;

/*
 * SetDistance should return a number proportional to how dissimilar
 * the two sets are (with any metric)
 * */
interface SetDistance
{
	public function dist(array &$setA, array &$setB);
}

?>
