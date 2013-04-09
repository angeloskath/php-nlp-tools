<?php

include('../../autoloader.php');

use NlpTools\Random\Distributions\Dirichlet;
use NlpTools\Random\Generators\MersenneTwister;

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

/**
 * Draw once from a multinomial distribution
 */
function draw($d) {
	$mt = MersenneTwister::get(); // simply mt_rand but in the interval [0,1)
	$x = $mt->generate();
	$p = 0.0;
	foreach ($d as $i=>$v)
	{
		$p+=$v;
		if ($p > $x)
			return $i;
	}
}

function create_document($topic_dists,$theta,$length) {
	$doc = array_fill_keys(range(0,24),0);
	while ($length-- > 0)
	{
		$topic = draw($theta);
		$word = draw($topic_dists[$topic]);
		$doc[$word] += 1;
	}
	return array_map(
		function ($start) use($doc) {
			return array_slice($doc,$start,5);
		},
		range(0,24,5)
	);
}

$topics = array(
	array(
		array(1,1,1,1,1),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0)
	),
	array(
		array(0,0,0,0,0),
		array(1,1,1,1,1),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0)
	),
	array(
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(1,1,1,1,1),
		array(0,0,0,0,0),
		array(0,0,0,0,0)
	),
	array(
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(1,1,1,1,1),
		array(0,0,0,0,0)
	),
	array(
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(1,1,1,1,1)
	),
	array(
		array(0,0,0,0,1),
		array(0,0,0,0,1),
		array(0,0,0,0,1),
		array(0,0,0,0,1),
		array(0,0,0,0,1)
	),
	array(
		array(0,0,0,1,0),
		array(0,0,0,1,0),
		array(0,0,0,1,0),
		array(0,0,0,1,0),
		array(0,0,0,1,0)
	),
	array(
		array(0,0,1,0,0),
		array(0,0,1,0,0),
		array(0,0,1,0,0),
		array(0,0,1,0,0),
		array(0,0,1,0,0)
	),
	array(
		array(0,1,0,0,0),
		array(0,1,0,0,0),
		array(0,1,0,0,0),
		array(0,1,0,0,0),
		array(0,1,0,0,0)
	),
	array(
		array(1,0,0,0,0),
		array(1,0,0,0,0),
		array(1,0,0,0,0),
		array(1,0,0,0,0),
		array(1,0,0,0,0)
	)
);

$topics = array_map(
	function ($topic) {
		return array_map(
			function ($row) {
				return array_map(
					function ($pixel) {
						return (int)(255*$pixel);
					},
					$row
				);
			},
			$topic
		);
	},
	$topics
);

if (!file_exists('topics'))
	mkdir('topics');

array_walk(
	$topics,
	function ($topic,$key) {
		create_image($topic,"topics/topic-$key");
	}
);

$flat_topics = array_map(
	function ($topic) {
		$t = call_user_func_array(
			'array_merge',
			$topic
		);
		
		$total = array_sum($t);
		return array_map(
			function ($ti) use($total) {
				return $ti/$total;
			},
			$t
		);
	},
	$topics
);


$dir = new Dirichlet(
	1, // the a parameter a can also be a vector
	count($flat_topics) // the k dimensions for the dirichlet dist
);

if (!file_exists('data'))
	mkdir('data');
	
for ($i=0;$i<500;$i++)
{
	$doc = create_document($flat_topics,$dir->sample(),100);
	create_image($doc,"data/$i");
}

if (!file_exists('results'))
	mkdir('results');
