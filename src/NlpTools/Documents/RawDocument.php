<?php

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

/**
 * RawDocument simply encapsulates a php variable
 */
class RawDocument implements DocumentInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getDocumentData()
    {
        return $this->data;
    }

    public function applyTransformation(TransformationInterface $transform)
    {
        $this->data = $transform->transform($this->data);
    }
}
