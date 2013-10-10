<?php

namespace NlpTools\Utils;

/**
 * The identity transformer is for testing purposes. It implements
 * the TransformationInterface but it changes nothing so data after
 * applying this transformer should be exactly as they were.
 */
class IdentityTransformer implements TransformationInterface
{
    public function transform($value)
    {
        return $value;
    }
}
