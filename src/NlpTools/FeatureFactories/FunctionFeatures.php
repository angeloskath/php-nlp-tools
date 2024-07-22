<?php

declare(strict_types=1);

namespace NlpTools\FeatureFactories;

use NlpTools\Documents\DocumentInterface;

/**
 * An implementation of FeatureFactoryInterface that takes any number of callables
 * (function names, closures, array($object,'func_name'), etc.) and
 * calls them consecutively using the return value as a feature's unique
 * string.
 *
 * The class can model both feature frequency and presence
 */
class FunctionFeatures implements FeatureFactoryInterface
{
    protected bool $frequency = false;

    /**
     * @param array<int, mixed> $functions
     */
    public function __construct(protected array $functions = [])
    {
    }

    /**
     * Set the feature factory to model frequency instead of presence
     */
    public function modelFrequency(): void
    {
        $this->frequency = true;
    }

    /**
     * Set the feature factory to model presence instead of frequency
     */
    public function modelPresence(): void
    {
        $this->frequency = false;
    }

    /**
     * Add a function as a feature
     */
    public function add(callable $feature): void
    {
        $this->functions[] = $feature;
    }

    /**
     * Compute the features that "fire" for a given class,document pair.
     *
     * Call each function one by one. Eliminate each return value that
     * evaluates to false. If the return value is a string add it to
     * the feature set. If the return value is an array iterate over it
     * and add each value to the feature set.
     *
     * @return array<int, mixed>
     */
    public function getFeatureArray(string $class, DocumentInterface $document): array
    {
        $features = array_filter(
            array_map(
                fn($feature): mixed => call_user_func($feature, $class, $document),
                $this->functions
            )
        );
        $set = [];
        foreach ($features as $feature) {
            if (is_array($feature)) {
                foreach ($feature as $ff) {
                    if (!isset($set[$ff])) {
                        $set[$ff] = 0;
                    }

                    $set[$ff]++;
                }
            } else {
                if (!isset($set[$feature])) {
                    $set[$feature] = 0;
                }

                $set[$feature]++;
            }
        }

        if ($this->frequency) {
            return $set;
        }

        return array_keys($set);
    }
}
