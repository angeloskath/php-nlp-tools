<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;
use NlpTools\Documents\DocumentInterface;

/**
 * A TrainingDocument is a document that "decorates" any other document
 * to add the real class of the document. It is used while training
 * together with the training set.
 */
class TrainingDocument implements DocumentInterface
{
    /**
     * @param string            $class The actual class of the Document $d
     * @param DocumentInterface $document The document to be decorated
     */
    public function __construct(protected string $class, protected DocumentInterface $document)
    {
    }

    /**
     * @return array<int, string>
     */
    public function getDocumentData(): array
    {
        return $this->document->getDocumentData();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Pass the transformation to the decorated document
     */
    public function applyTransformation(TransformationInterface $transformation): void
    {
        $this->document->applyTransformation($transformation);
    }
}
