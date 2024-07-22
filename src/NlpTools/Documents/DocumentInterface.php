<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Utils\TransformationInterface;

/**
 * A Document is a representation of a Document to be classified.
 * It can be a representation of a word, of a bunch of text, of a text
 * that has structure (ex.: Title,Body,Link)
 */
interface DocumentInterface
{
    /**
     * Return the data of what is being represented. If it were a word
     * we could return a word. If it were a blog post we could return
     * an array(Title,Body,array(Comments)).
     */
    public function getDocumentData(): mixed;

    /**
     * Apply the transformation to the data of this document.
     * How the transformation is applied (per token, per token sequence, etc)
     * is decided by the implementing classes.
     */
    public function applyTransformation(TransformationInterface $transformation): void;

    public function getClass(): string;
}
