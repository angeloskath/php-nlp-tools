<?php

namespace NlpTools\Documents;

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
