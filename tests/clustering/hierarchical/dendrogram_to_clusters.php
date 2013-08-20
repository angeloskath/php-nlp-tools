<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Clustering\Hierarchical as HC;

$dendrograms = array(
	array(
		array(array(0,1),array(array(2,3),4)),
		array(array(0,1),array(2,3,4))
	),
	array(
		array(array(0,array(1,array(2,array(3,array(4,array(5,array(6,7)))))))),
		array(array(0),array(1),array(2),array(3,4,5,6,7))
	)
);

//$i = 1;
//print_r(
//	HC::dendrogramToClusters($dendrograms[$i][0],count($dendrograms[$i][1]))
//);
//die;

foreach ($dendrograms as $i=>$d)
{
	_assert(
		$d[1]==HC::dendrogramToClusters($d[0],count($d[1])),
		"Error transforming dendrogram $i"
	);
}
