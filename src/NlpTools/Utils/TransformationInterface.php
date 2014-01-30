<?php

namespace NlpTools\Utils;

/**
 * TransformationInterface represents any type of transformation
 * to be applied upon documents. The transformation is defined upon
 * single values and how each document applies a transformation
 * differs. For instance TokensDocument should apply the transformation
 * on each token but EuclideanDocument could apply it on each key (dimension).
 *
 * There can be combinations of transformations and documents that make
 * no sense. For instance if we have a scaling transformation that expects
 * numeric values and returns them multiplied by a constant c, it
 * would make little sense to pass this transformation to
 * TokensDocument that expects transformations to be applied on
 * specific tokens.
 */
interface TransformationInterface
{
    /**
     * Return the value transformed.
     * @param  mixed $value The value to transform
     * @return mixed
     */
    public function transform($value);
}
