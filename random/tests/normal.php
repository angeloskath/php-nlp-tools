<?php

include ('../vendor/autoload.php');

use NlpTools\Random\Distributions\Normal as NormalDistribution;

function mean($samples) {
	return array_sum($samples)/count($samples);
}
function stdev($samples,$m=null) {
	if ($m==null)
		$m = mean($samples);
	
	return sqrt(
		array_sum(
			array_map(
				function ($x) use($m) {
					$t = ($x-$m);
					return $t*$t;
				},
				$samples
			)
		)
		/
		count($samples)-1
	);
}

$normal = new NormalDistribution(10,5);

$samples = array();
for ($i=0;$i<10000;$i++) {
	$samples[] = $normal->sample();
}

var_dump(
	mean($samples),
	stdev($samples)
);

