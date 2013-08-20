<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Documents\TokensDocument;
use NlpTools\Clustering\Hierarchical as HierarchicalClusterer;
use NlpTools\Clustering\MergeStrategies\SingleLink;
use NlpTools\Similarity\Euclidean;

$points = array(
	array(
		'x'=>1,
		'y'=>1
	),
	array(
		'x'=>1,
		'y'=>2
	),
	array(
		'x'=>2,
		'y'=>2
	),
	array(
		'x'=>3,
		'y'=>3
	),
	array(
		'x'=>3,
		'y'=>4
	),
);
$tset = new TrainingSet();
foreach ($points as $p)
	$tset->addDocument('',new TokensDocument($p));


$hc = new HierarchicalClusterer(
	new SingleLink(), // use the single link strategy
	new Euclidean() // with euclidean distance
);

list($clusters) = $hc->cluster($tset,new DataAsFeatures());
print_r(HierarchicalClusterer::dendrogramToClusters($clusters,2));
_assert(
	$clusters == array(
		array(
			array(
				array(
					0,
					1
				),
				2
			),
			array(
				3,
				4
			)
		)
	),
	"Wrong clustering!"
);


