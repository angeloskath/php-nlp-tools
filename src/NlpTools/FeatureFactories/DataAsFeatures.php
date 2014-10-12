<?php
namespace NlpTools\FeatureFactories;

use NlpTools\FeatureVector\ArrayFeatureVector;
use NlpTools\Documents\DocumentInterface;

class DataAsFeatures implements FeatureFactoryInterface
{
    /**
     * For use with TokensDocument mostly. Simply return the data as
     * features. Could contain duplicates (a feature firing twice in
     * for a signle document).
     *
     * @param  string            $class The class for which we are calculating features
     * @param  DocumentInterface $d     The document to calculate features for.
     * @return ArrayFeatureVector
     */
    public function getFeatureArray($class, DocumentInterface $d)
    {
        return new ArrayFeatureVector($d->getDocumentData());
    }
}
