<?php

namespace NlpTools\FeatureVector;

/**
 * Simple FeatureVector that stores the features a sparse matrix by key (map).
 *
 * If the array passed as a constructor parameter has integer keys it simply
 * transforms it to a map using array_count_values()
 */
class ArrayFeatureVector extends FeatureVector
{
    /**
     * This is our feature map
     */
    protected $features;

    /**
     * Transform the array to a map usin array_count_values if needed and store
     * the map as the features.
     */
    public function __construct(array $raw_features=array())
    {
        if (is_int(key($raw_features)))
            $this->features = array_count_values($raw_features);
        else
            $this->features = $raw_features;
    }

    /**
     * Count returns the number of active features in this feature vector.
     */
    public function count()
    {
        return count($this->features);
    }

    /**
     * Get if the feature named $feature_name exists
     */
    public function offsetExists($feature_name)
    {
        return isset($this->features[$feature_name]);
    }

    /**
     * Get the value of the feature named $feature_name
     */
    public function offsetGet($feature_name)
    {
        return $this->features[$feature_name];
    }

    /**
     * Iterate over all feature_name => value pairs.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->features);
    }
}
