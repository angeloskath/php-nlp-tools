<?php

namespace NlpTools;

interface Classifier
{
	/*
	 * Decide in which class C member of $classes would $d fit best.
	 * 
	 * name: classify
	 * @param $classes A set of classes
	 * @param $d A Document
	 * @return string A class
	 */
	public function classify(array $classes, Document $d);
}

?>
