<?php
namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Analysis\Idf;

 
class TfIdfFeatureFactory extends FunctionFeatures
{
    protected $stats;
 
    public function __construct(Idf $stats, array $functions)
    {
        parent::__construct($functions);
        $this->modelFrequency();
        $this->stats = $stats;
    }
 
    public function getFeatureArray($class, DocumentInterface $doc)
    {

        $frequencies = parent::getFeatureArray($class, $doc);

        foreach ($frequencies as $term=>&$value) {
            $value = ($value != 0) ? $value * $this->stats->idf($term) : 0;
        }

        return $frequencies;
    }


}