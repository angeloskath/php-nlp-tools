<?php
namespace NlpTools\FeatureFactories;
 
class TfFeatureFactory extends FunctionFeatures
{
 
    public function __construct(array $functions)
    {
        parent::__construct($functions);
        $this->modelFrequency();
    }

}