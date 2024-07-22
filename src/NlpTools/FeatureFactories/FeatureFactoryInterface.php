<?php

declare(strict_types=1);

namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;

interface FeatureFactoryInterface
{
    /**
     * Return an array with unique strings that are the features that
     * "fire" for the specified Document $d and class $class
     *
     * @return array<int, mixed>
     */
    public function getFeatureArray(string $class, DocumentInterface $document): array;
}
