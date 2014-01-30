<?php

namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;

interface FeatureFactoryInterface
{
    /**
     * Return an array with unique strings that are the features that
     * "fire" for the specified Document $d and class $class
     *
     * @param  string            $class The class for which we are calculating features
     * @param  DocumentInterface $d     The document for which we are calculating features
     * @return array
     */
    public function getFeatureArray($class, DocumentInterface $d);
}
