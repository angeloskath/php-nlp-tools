<?php

namespace NlpTools;

/*
 * An implementation of FeatureFactory that takes any number of callables
 * (function names, closures, array($object,'func_name'), etc.) and
 * calls them consecutively using the return value as a feature's unique
 * string.
 */
class FunctionFeatures implements FeatureFactory
{
	
	protected $functions;
	public function __construct(array $f=array()) {
		$this->functions=$f;
	}
	public function add( $feature ) {
		$this->functions[] = $feature;
	}
	
	/*
	 * Call each function one by one. Eliminate each return value that
	 * evaluates to false. If the return value is a string add it to
	 * the feature set. If the return value is an array iterate over it
	 * and add each value to the feature set.
	 * 
	 * name: getFeatureArray
	 * @param $class The class for which we are calculating features
	 * @param $d The document for which we are calculating features
	 * @return array
	 */
	public function getFeatureArray($class, Document $d) {
		$features = array_filter(
			array_map( function ($feature) use($class,$d) {
					return call_user_func($feature, $class, $d);
				},
				$this->functions
			));
		$set = array();
		foreach ($features as $f)
		{
			if (is_array($f))
			{
				foreach ($f as $ff)
				{
					$set[$ff] = 1;
				}
			}
			else
			{
				$set[$f] = 1;
			}
		}
		return array_keys($set);
	}
	
}

?>
