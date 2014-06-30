<?php

namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;

/**
 * LinearChainCRFFeatures eases the creation of features used in linear chain
 * CRFs. This feature factory decorates two feature factories (one is optional)
 * that target the single current class and the class chain accordingly. It
 * also adds the chain itsself so the model can take into account the
 * transition itsself regardless the current document.
 */
class LinearChainCRFFeatures implements FeatureFactoryInterface
{
    protected $singleClassFeats;
    protected $chainFeats;

    /**
     * @param FeatureFactoryInterface $singleClassFeats This feature factory will be
     *                                                  passed just this document's class
     * @param FeatureFactoryInterface $chainFeats       This feature factory will be
     *                                                  passed the whole class chain
     */
    public function __construct(
        FeatureFactoryInterface $singleClassFeats,
        FeatureFactoryInterface $chainFeats = null
    )
    {
        $this->singleClassFeats = new MaxentFeatures($singleClassFeats);
        if ($chainFeats)
            $this->chainFeats = new MaxentFeatures($chainFeats);
    }

    /**
     * Except for the features targeting the
     */
    public function getFeatureArray($class, DocumentInterface $doc)
    {
        $classlist = explode("|", $class);
        $ourclass = array_pop($classlist);

        return array_merge(
            array($class),
            $this->singleClassFeats->getFeatureArray($ourclass, $doc),
            ($this->chainFeats!==null) ? $this->chainFeats->getFeatureArray($class, $doc) : array()
        );
    }
}
