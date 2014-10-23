<?php

namespace NlpTools\FeatureFactories;

use NlpTools\FeatureVector\ArrayFeatureVector;
use NlpTools\Documents\DocumentInterface;

/**
 * In Maxent models we need to target the features to specific classes. Instead
 * of writing the functions each time by hand this decorator will transform any
 * feature factory to one targeting Maxent models.
 *
 * It does the above by prepending "$class ^ " to each feature.
 */
class MaxentFeatures implements FeatureFactoryInterface
{
    protected $ff;

    public function __construct(FeatureFactoryInterface $ff)
    {
        $this->ff = $ff;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatureArray($class, DocumentInterface $doc)
    {
        $feats = $this->ff->getFeatureArray($class, $doc);
        $newfeatures = array();
        foreach ($feats as $f=>$v) {
            $newfeatures["$class ^ $f"] = $v;
        }

        return new ArrayFeatureVector($newfeatures);
    }
}
