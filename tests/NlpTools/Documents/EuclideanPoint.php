<?php

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

class EuclideanPoint implements DocumentInterface
{
    public $x;
    public $y;

    public function __construct($x,$y)
    {
        $this->x = $x;
        $this->y = $y;
    }
    public function getDocumentData()
    {
        return array(
            'x'=>$this->x,
            'y'=>$this->y
        );
    }

    public static function getRandomPointAround($x,$y,$R)
    {
        return new EuclideanPoint(
            $x+mt_rand(-$R,$R),
            $y+mt_rand(-$R,$R)
        );
    }

    public function applyTransformation(TransformationInterface $transform)
    {
        $this->x = $transform->transform($this->x);
        $this->y = $transform->transform($this->y);
    }
}
