<?php

namespace NlpTools\Similarity;

/**
 * SetDistance should return a number proportional to how dissimilar
 * the two instances are(with any metric)
 */
interface Distance
{
	public function dist(&$A, &$B);
}

?>
