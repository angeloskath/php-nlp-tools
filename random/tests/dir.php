<?php

include ('../vendor/autoload.php');

use NlpTools\Random\Distributions\Dirichlet;

$dir = new Dirichlet(1,10);

$sample = $dir->sample();
var_dump(
	$sample,
	array_sum($sample)
);
