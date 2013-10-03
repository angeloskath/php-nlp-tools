<?php

namespace NlpTools\Documents;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;
use \BadMethodCallException;

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

    /**
     * @throws 
     * @param TokenTransformationInterface $transformer 
     */
    public function applyTransformation(TokenTransformationInterface $transformer)
    {
        throw new BadMethodCallException("Not applicable");
    }
}
