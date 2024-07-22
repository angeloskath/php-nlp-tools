<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

class EuclideanPoint implements DocumentInterface
{
    public function __construct(public int $x, public int $y)
    {
    }

    /**
     * @return array<string, int>
     */
    public function getDocumentData(): array
    {
        return ['x' => $this->x, 'y' => $this->y];
    }

    public static function getRandomPointAround(int $x, int $y, int $R): EuclideanPoint
    {
        return new EuclideanPoint(
            $x + mt_rand(-$R, $R),
            $y + mt_rand(-$R, $R)
        );
    }

    public function applyTransformation(TransformationInterface $transformation): void
    {
        $this->x = (int) $transformation->transform((string) $this->x);
        $this->y = (int) $transformation->transform((string) $this->y);
    }

    public function getClass(): string
    {
        return self::class;
    }
}
