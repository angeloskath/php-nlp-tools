<?php

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

/**
 * A TrainingDocument is a document that "decorates" any other document
 * to add the real class of the document. It is used while training
 * together with the training set.
 */
class TrainingDocument implements DocumentInterface
{
    protected $d;
    protected $class;

    /**
     * @param string            $class The actual class of the Document $d
     * @param DocumentInterface $d     The document to be decorated
     */
    public function __construct($class, DocumentInterface $d)
    {
        $this->d = $d;
        $this->class = $class;
    }
    public function getDocumentData()
    {
        return $this->d->getDocumentData();
    }
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Pass the transformation to the decorated document
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        $this->d->applyTransformation($transform);
    }
}
