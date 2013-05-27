<?php

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

function draw_clusters($tset, $clusters, $centroids=null, $lines=False,$w=300,$h=200) {
	if (!function_exists('imagecreate'))
		return null;
	
	$im = imagecreatetruecolor($w,$h);
	$white = imagecolorallocate($im,255,255,255);
	$colors = array();
	$NC = count($clusters);
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
		if (is_array($centroids))
		{
			$x = $centroids[$cid]['x'];
			$y = $centroids[$cid]['y'];
			if ($lines)
			{
				// draw line
				// for cosine similarity
				//imagesetthickness($im,5);
				//imageline($im,0,0,$x*400,$y*400,$colors[$cid]);
			}
			else
			{
				// draw circle for euclidean
				imagefilledarc($im,$x,$y,10,10,0,360,$colors[$cid],0);
			}
		}
	}
	return $im;
}
