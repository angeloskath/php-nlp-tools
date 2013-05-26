<?php

include ('../../../autoloader.php');
include ('../../testing.php');
include ('../cluster_testing.php');

use NlpTools\Documents\TrainingSet;
use NlpTools\Clustering\MergeStrategies\SingleLink;
use NlpTools\Clustering\MergeStrategies\CompleteLink;
use NlpTools\Clustering\MergeStrategies\GroupAverage;
use NlpTools\Similarity\Euclidean;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Clustering\Hierarchical as HierarchicalClusterer;

if (isset($argv[1]))
	$N = (int)$argv[1];
else
	$N = 500;

$tset = new TrainingSet();
for ($i=0;$i<$N;$i++) {
	$tset->addDocument(
		'',
		EuclideanPoint::getRandomPointAround(100,100,45)
	);
}
for ($i=0;$i<$N;$i++) {
	$tset->addDocument(
		'',
		EuclideanPoint::getRandomPointAround(200,100,45)
	);
}

$hc = new HierarchicalClusterer(
	new SingleLink(),
	new Euclidean()
);

$s = microtime(true);
list($clusters) = $hc->cluster($tset,new DataAsFeatures());
echo microtime(true)-$s,PHP_EOL;

$clusters = HierarchicalClusterer::dendrogramToClusters($clusters,2);

$im = draw_clusters(
	$tset,
	$clusters,
	null, // no centroids
	false // no lines
);

if ($im)
	imagepng($im,'clusters.png');
else
	print_r($clusters);


