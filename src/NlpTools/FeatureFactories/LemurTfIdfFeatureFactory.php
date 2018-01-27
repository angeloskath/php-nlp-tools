<?php
namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Analysis\Idf;

 
class LemurTfIdfFeatureFactory extends FunctionFeatures
{
    protected $idf;
  
    const B = 0.75;

    const K = 1.2;

    public function __construct(Idf $idf, array $functions)
    {
        parent::__construct($functions);
        $this->modelFrequency();
        $this->idf = $idf;
        $this->b = self::B;
        $this->k = self::K;
    }
 
    public function getFeatureArray($class, DocumentInterface $doc)
    {

        $frequencies = parent::getFeatureArray($class, $doc);
        $length = count($doc->getDocumentData());
        $numberofTokens = $this->idf->numberofCollectionTokens();
        $avg_dl = $length/$numberofTokens;
        foreach ($frequencies as $term=>&$value) {
               $value = ($value != 0) ? (($value * $this->k) / ($value + $this->k * (1 - $this->b + $this->b * ($length / $avg_dl)))) * $this->idf->idf($term) : 0;
        }

        return $frequencies;
    }


}