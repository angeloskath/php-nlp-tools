<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use NlpTools\Clustering\KMeans;
use NlpTools\Similarity\Euclidean;
use NlpTools\Similarity\CosineSimilarity;
use NlpTools\Clustering\CentroidFactories\MeanAngle;
use NlpTools\Clustering\CentroidFactories\Euclidean as EuclidCF;
use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Documents\Document;

class EuclideanPoint implements Document
{
	public $x;
	public $y;

	public function __construct($x,$y) {
		$this->x = $x;
		$this->y = $y;
	}
	public function getDocumentData() {
		return array(
			'x'=>$this->x,
			'y'=>$this->y
		);
	}

	public static function getRandomPointAround($x,$y,$R) {
		return new EuclideanPoint(
			$x+mt_rand(-$R,$R),
			$y+mt_rand(-$R,$R)
		);
	}
}

function getColor($t) {
	$u = function ($x) { return ($x>0) ? 1 : 0; };
	$pulse = function ($x,$a,$b) use($u) { return $u($x-$a)-$u($x-$b); };
	return array(
		(int)( 255*( $pulse($t,0,1/3) + $pulse($t,1/3,2/3)*(2-3*$t) ) ),
		(int)( 255*( $pulse($t,0,1/3)*3*$t + $pulse($t,1/3,2/3) + $pulse($t,2/3,1)*(3-3*$t) ) ),
		(int)( 255*( $pulse($t,1/3,2/3)*(3*$t-1) + $pulse($t,2/3,1) ) )
	);
}
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

if (function_exists('imagecreate'))
{
	$im = imagecreatetruecolor(300,200);
	$white = imagecolorallocate($im,255,255,255);
	$colors = array();
	for ($i=1;$i<=$NC;$i++) {
		list($r,$g,$b) = getColor($i/$NC);
		$colors[] = imagecolorallocate($im,$r,$g,$b);
	}

	imagefill($im,0,0,$white);
	foreach ($clusters as $cid=>$cluster)
	{
		foreach ($cluster as $idx)
		{
			$data = $tset[$idx]->getDocumentData();
			imagesetpixel($im,$data['x'],$data['y'],$colors[$cid]);
		}
		$x = $centroids[$cid]['x'];
		$y = $centroids[$cid]['y'];
		// draw line
		// for cosine similarity
		//imagesetthickness($im,5);
		//imageline($im,0,0,$x*400,$y*400,$colors[$cid]);
		
		// draw circle for euclidean
		imagefilledarc($im,$x,$y,10,10,0,360,$colors[$cid],0);
	}

	imagepng($im,'clusters.png');
}
else
{
	var_dump($clusters);
}


