<?php

namespace NlpTools\Classifiers;

interface Classifier
{
	/**
	 * Decide in which class C member of $classes would $d fit best.
	 * 
	 * @param array $classes A set of classes
	 * @param Document $d A Document
	 * @return string A class
	 */
	public function classify(array $classes, \NlpTools\Documents\Document $d);
}

?>
