<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

/**
 * RawDocument simply encapsulates a php variable
 */
class RawDocument implements DocumentInterface
{
    public function __construct(protected ?string $data)
    {
    }

    public function getDocumentData(): ?string
    {
        return $this->data;
    }

    public function applyTransformation(TransformationInterface $transformation): void
    {
        $this->data = $transformation->transform($this->data);
    }

    public function getClass(): string
    {
        return self::class;
    }
}
