<?php

function create_image(array $img,$filename) {
	$im = imagecreate(5,5);
	imagecolorallocate($im,0,0,0);
	foreach ($img as $y=>$row) {
		foreach ($row as $x=>$color) {
			$c = imagecolorallocate($im,$color,$color,$color);
			imagesetpixel($im,$x,$y,$c);
		}
	}
	imagepng($im,$filename);
}

function draw($d) {
	
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
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(1,1,1,1,1)
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
		array(1,1,1,1,1),
		array(0,0,0,0,0),
		array(0,0,0,0,0)
	),
	array(
		array(0,0,0,0,0),
		array(1,1,1,1,1),
		array(0,0,0,0,0),
		array(0,0,0,0,0),
		array(0,0,0,0,0)
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

array_walk(
	$topics,
	function ($topic,$key) {
		create_image($topic,"topics/topic-$key");
	}
);


