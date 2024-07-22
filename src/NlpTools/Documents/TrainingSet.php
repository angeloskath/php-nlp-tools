<?php

declare(strict_types=1);

namespace NlpTools\Documents;

use NlpTools\Documents\DocumentInterface;
use NlpTools\Utils\TransformationInterface;

/**
 * A collection of TrainingDocument objects. It implements many built
 * in php interfaces for ease of use.
 *
 * @implements \Iterator<int|string, DocumentInterface>
 * @implements \ArrayAccess<int|string, DocumentInterface>
 */
class TrainingSet implements \Iterator, \ArrayAccess, \Countable
{
    public const CLASS_AS_KEY = 1;

    public const OFFSET_AS_KEY = 2;

    /**
     * An array that contains all the classes present in the TrainingSet
     *
     * @var array<string, int>
     */
    protected array $classSet = [];

    /**
     * The documents container
     *
     * @var array<int, DocumentInterface>
     */
    protected array $documents = [];

    // When iterated upon what should the key be?
    protected int $keytype = self::CLASS_AS_KEY;

    // When iterated upon the currentDocument
    protected DocumentInterface|false $currentDocument;

    /**
     * Add a document to the set.
     */
    public function addDocument(string $class, DocumentInterface $document): void
    {
        $this->documents[] = new TrainingDocument($class, $document);
        $this->classSet[$class] = 1;
    }

    /**
     * Return the classset
     *
     * @return array<int, string>
     */
    public function getClassSet(): array
    {
        return array_keys($this->classSet);
    }

    /**
     * Decide what should be returned as key when iterated upon
     */
    public function setAsKey(int $what): void
    {
        $this->keytype = match ($what) {
            self::CLASS_AS_KEY, self::OFFSET_AS_KEY => $what,
            default => self::CLASS_AS_KEY,
        };
    }

    /**
     * Apply an array of transformations to all documents in this container.
     *
     * @param array<TransformationInterface> $transforms An array of TransformationInterface instances
     */
    public function applyTransformations(array $transforms): void
    {
        foreach ($this->documents as $document) {
            foreach ($transforms as $transform) {
                $document->applyTransformation($transform);
            }
        }
    }

    // ====== Implementation of \Iterator interface =========
    public function rewind(): void
    {
        reset($this->documents);
        $this->currentDocument = current($this->documents);
    }

    public function next(): void
    {
        $this->currentDocument = next($this->documents);
    }

    public function valid(): bool
    {
        return $this->currentDocument !== false;
    }

    public function current(): DocumentInterface
    {
        return $this->currentDocument;
    }

    public function key(): string
    {
        return match ($this->keytype) {
            self::CLASS_AS_KEY => $this->currentDocument->getClass(),
            self::OFFSET_AS_KEY => key($this->documents),
            default => throw new \Exception("Undefined type as key"),
        };
    }

    // === Implementation of \Iterator interface finished ===

    // ====== Implementation of \ArrayAccess interface =========
    public function offsetSet($key, $value): void
    {
        throw new \Exception("Shouldn't add documents this way, add them through addDocument()");
    }

    public function offsetUnset($key): void
    {
        throw new \Exception("Cannot unset any document");
    }

    public function offsetGet($key): DocumentInterface
    {
        return $this->documents[$key];
    }

    public function offsetExists($key): bool
    {
        return isset($this->documents[$key]);
    }

    // === Implementation of \ArrayAccess interface finished ===

    // implementation of \Countable interface
    public function count(): int
    {
        return count($this->documents);
    }
}
