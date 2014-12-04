<?php

namespace NlpTools\FeatureVector;

/**
 * UnionFeatureVector merges two or more feature vectors together.
 *
 * Example usage can be found in the LinearChainCRFFeatures. The feature
 * vectors are treated as disjoint sets.
 */
class UnionFeatureVector extends FeatureVector
{
    /**
     * Hold the unioned feature vectors
     */
    protected $fvs;
    /**
     * Cached count result. The sum of calling count on every element in the
     * fvs array above.
     */
    protected $featureCount;

    public function __construct(array $fvs)
    {
        foreach ($fvs as $fv) {
            if (!($fv instanceof FeatureVector)) {
                throw new \InvalidArgumentException("UnionFeatureVector accepts only feature vectors");
            }
        }

        $this->fvs = $fvs;
    }

    /**
     * The sum of the feature counts in every feature vector.
     */
    public function count()
    {
        if ($this->featureCount) {
            return $this->featureCount;
        }

        $this->featureCount = array_sum(array_map('count', $this->fvs));

        return $this->featureCount;
    }

    /**
     * Does the feature exist in any of the passed in feature vectors.
     */
    public function offsetExists($featureName)
    {
        foreach ($this->fvs as $fv) {
            if (isset($fv[$featureName])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the value of the feature named featureName from whichever feature
     * vector it exists in
     */
    public function offsetGet($featureName)
    {
        foreach ($this->fvs as $fv) {
            if (isset($fv[$featureName])) {
                return $fv[$featureName];
            }
        }

        // should we be throwing an exception?
        return null;
    }

    /**
     * Return an iterator that will iterate over all the features.
     */
    public function getIterator()
    {
        $it = new \AppendIterator();
        foreach ($this->fvs as $fv) {
            $it->append($fv->getIterator());
        }

        return $it;
    }
}
