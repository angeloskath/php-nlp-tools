<?php

declare(strict_types=1);

namespace NlpTools\Utils;

use NlpTools\Classifiers\ClassifierInterface;
use NlpTools\Documents\RawDocument;

/**
 * Classify whatever is passed in the transform and pass it a different set
 * of transformations based on the class.
 *
 * Can be used to create, for instance, language based transformations.
 */
class ClassifierBasedTransformation implements TransformationInterface
{
    /**
     * @var array<string, mixed>
     */
    protected array $transforms;

    /**
     * @var array<int, string>
     */
    protected array $classes = [];

    /**
     * In order to classify anything with NlpTools we need something
     * that implements the ClassifierInterface. We also need the set
     * of classes but that will be calculated by the classes for which
     * we register a transformation.
     */
    public function __construct(protected ClassifierInterface $classifier)
    {
    }

    /**
     * Classify the passed in variable w and then apply each transformation
     * to the output of the previous one.
     */
    public function transform(string $w): string
    {
        $class = $this->classifier->classify(
            $this->classes,
            new RawDocument($w)
        );

        foreach ($this->transforms[$class] as $t) {
            $w = $t->transform($w);
        }

        return $w;
    }

    /**
     * Register a set of transformations for a given class.
     *
     * @param array<string, mixed>|TransformationInterface $transforms Either an array of transformations or a single transformation
     */
    public function register(string $class, array|TransformationInterface $transforms): void
    {
        if (!is_array($transforms)) {
            $transforms = [$transforms];
        }

        foreach ($transforms as $t) {
            if (!($t instanceof TransformationInterface)) {
                throw new \InvalidArgumentException("Only instances of TransformationInterface can be registered");
            }
        }

        if (!isset($this->transforms[$class])) {
            $this->classes[] = $class;
            $this->transforms[$class] = [];
        }

        foreach ($transforms as $transform) {
            $this->transforms[$class][] = $transform;
        }
    }
}
