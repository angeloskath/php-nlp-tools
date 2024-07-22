<?php

declare(strict_types=1);

namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;

class DataAsFeatures implements FeatureFactoryInterface
{
    /**
     * For use with TokensDocument mostly. Simply return the data as
     * features. Could contain duplicates (a feature firing twice in
     * for a single document).
     */
    public function getFeatureArray(string $class, DocumentInterface $document): array
    {
        return $document->getDocumentData();
    }
}
