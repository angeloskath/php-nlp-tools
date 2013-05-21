<?php

include ('../../../autoloader.php');
include ('../../testing.php');
include ('../cluster_testing.php');

use NlpTools\Documents\TrainingSet;
use NlpTools\Similarity\SingleLink;
use NlpTools\Similarity\Euclidean;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Clustering\Hierarchical as HierarchicalClusterer;

$tset = new TrainingSet();
for ($i=0;$i<200;$i++) {
	$tset->addDocument(
		'',
		EuclideanPoint::getRandomPointAround(100,100,45)
	);
}
for ($i=0;$i<200;$i++) {
	$tset->addDocument(
		'',
		EuclideanPoint::getRandomPointAround(200,100,45)
	);
}

$hc = new HierarchicalClusterer(
	new SingleLink(
		new Euclidean()
	)
);
list($clusters) = $hc->cluster($tset,new DataAsFeatures());
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


