<?php

namespace NlpTools;

class FunctionFeatures implements FeatureFactory
{
	
	protected $functions;
	public function __construct(array $f=array()) {
		$this->functions=$f;
	}
	public function add( $feature ) {
		$this->functions[] = $feature;
	}
	
	public function getFeatureArray($class, array $tokens) {
		return array_filter(
			array_map( function ($feature) use($class,$tokens) {
					return call_user_func($feature, $class, $tokens);
				},
				$this->functions
			));
			
	}
	
}

?>
