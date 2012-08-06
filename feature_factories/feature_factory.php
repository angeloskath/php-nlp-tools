<?php

namespace NlpTools;

interface FeatureFactory
{
	/*
	 * Return an array with unique strings that are the features that
	 * "fire" for the specified Document $d and class $class
	 * 
	 * name: getFeatureArray
	 * @return array
	 */
	public function getFeatureArray($class, Document $d);
}

?>
