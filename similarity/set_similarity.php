<?php

namespace NlpTools\Similarity;

/*
 * Set similarity should return a number that is proportional to how
 * similar those two sets are (with any metric).
 * 
 * */
interface SetSimilarity
{
	public function similarity(array &$setA, array &$setB);
}

?>
