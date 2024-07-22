<?php

declare(strict_types=1);

namespace NlpTools\Classifiers;

use NlpTools\Documents\DocumentInterface;

interface ClassifierInterface
{
    /**
     * Decide in which class C member of $classes would $d fit best.
     *
     * @param array<int, string> $classes
     */
    public function classify(array $classes, DocumentInterface $document): string;
}
