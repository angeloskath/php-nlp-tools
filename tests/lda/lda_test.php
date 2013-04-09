<?php

include ('../../autoloader.php');
include ('../testing.php');

use NlpTools\Models\Lda;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\TokensDocument;
use NlpTools\FeatureFactories\DataAsFeatures;

/**
 * Save a two dimensional array as a grey-scale image
 */
function create_image(array $img,$filename) {
	$im = imagecreate(count($img),count(current($img)));
	imagecolorallocate($im,0,0,0);
	foreach ($img as $y=>$row) {
		foreach ($row as $x=>$color) {
			$color = min(255,max(0,$color));
			$c = imagecolorallocate($im,$color,$color,$color);
			imagesetpixel($im,$x,$y,$c);
		}
	}
	imagepng($im,$filename);
}

function from_img($file) {
	$im = imagecreatefrompng($file);
	$d = array();
	for ($w=0;$w<25;$w++)
	{
		$x = (int)($w%5);
		$y = (int)($w/5);
		
		$c = imagecolorsforindex($im,imagecolorat($im,$x,$y));
		$c = $c['red'];
		if ($c>0)
		{
			$d = array_merge(
				$d,
				array_fill_keys(
					range(0,$c-1),
					$w
				)
			);
		}
	}
	return $d;
}

$tset = new TrainingSet();
for ($i=0;$i<500;$i++) {
	$f = "data/$i";
	$tset->addDocument(
		'',
		new TokensDocument(from_img($f))
	);
}

$lda = new Lda(new DataAsFeatures(),10,1,1);
$docs = $lda->generateDocs($tset);
$lda->initialize($docs);

$i = 100;
while ($i-- > 0)
{
	$lda->gibbsSample($docs);
	$topics = $lda->getPhi();
	echo $lda->getLogLikelihood(),PHP_EOL;
	foreach ($topics as $t=>$topic)
	{
		$it = 100-$i;
		$name = sprintf("results/topic-%04d-%04d",$it,$t);
		$max = max($topic);
		create_image(
			array_map(
				function ($x) use($topic,$max) {
					return array_map(
						function ($y) use($x,$topic,$max) {
							return (int)(($topic[$y*5+$x]/$max)*255);
						},
						range(0,4)
					);
				},
				range(0,4)
			),
			$name
		);
	}	
}
