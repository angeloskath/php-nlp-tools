<?php

include ('../../../autoloader.php');
include ('../../testing.php');
include ('../cluster_testing.php');

use NlpTools\Clustering\KMeans;
use NlpTools\Similarity\Euclidean;
use NlpTools\Similarity\CosineSimilarity;
use NlpTools\Clustering\CentroidFactories\MeanAngle;
use NlpTools\Clustering\CentroidFactories\Euclidean as EuclidCF;
use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Documents\Document;

$NC = 2; // number of clusters
$clust = new Kmeans(
	$NC,
	new Euclidean(),
	new EuclidCF(),
	0.001
);

$tset = new TrainingSet();
for ($i=0;$i<500;$i++) {
	$tset->addDocument(
		'',
		EuclideanPoint::getRandomPointAround(100,100,45)
	);
}
for ($i=0;$i<500;$i++) {
	$tset->addDocument(
		'',
		EuclideanPoint::getRandomPointAround(200,100,45)
	);
}

list($clusters,$centroids,$distances) = $clust->cluster($tset,new DataAsFeatures());

$im = draw_clusters(
	$tset,
	$clusters,
	$centroids,
	false // lines or not
);

if ($im)
	imagepng($im,'clusters.png');
else
	var_dump($clusters);

