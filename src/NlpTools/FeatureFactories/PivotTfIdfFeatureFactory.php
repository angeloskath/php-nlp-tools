<?php
namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Analysis\PivotIdf;

 
class PivotTfIdfFeatureFactory extends FunctionFeatures
{
    protected $idf;

    protected $slope;

    const SLOPE = 0.20;
 
    public function __construct(PivotIdf $idf, array $functions)
    {
        parent::__construct($functions);
        $this->modelFrequency();
        $this->idf = $idf;
        $this->slope = self::SLOPE;
    }
 
    public function getFeatureArray($class, DocumentInterface $doc)
    {

        $frequencies = parent::getFeatureArray($class, $doc);
        $length = count($doc->getDocumentData());
        $numberofTokens = $this->idf->numberofCollectionTokens();
        $avg_dl = $length/$numberofTokens;
        foreach ($frequencies as $term=>&$value) {
             $value = ($value != 0) ?  (1+log(1+log($value))) / ((1-$this->slope) + ($this->slope * ($length / $avg_dl))) * $this->idf->idf($term) : 0;
        }

        return $frequencies;
    }


}