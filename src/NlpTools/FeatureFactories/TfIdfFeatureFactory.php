<?php
namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Analysis\Idf;

 
class TfIdfFeatureFactory extends FunctionFeatures
{
    protected $idf;
 
    public function __construct(Idf $idf, array $functions)
    {
        parent::__construct($functions);
        $this->modelFrequency();
        $this->idf = $idf;
    }
 
    public function getFeatureArray($class, DocumentInterface $doc)
    {

        $frequencies = parent::getFeatureArray($class, $doc);

        foreach ($frequencies as $term=>&$value) {
            $value = ($value != 0) ? $value * $this->idf->idf($term) : 0;
        }

        return $frequencies;
    }


}