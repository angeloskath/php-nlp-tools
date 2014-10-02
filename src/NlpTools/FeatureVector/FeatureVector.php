<?php

namespace NlpTools\FeatureVector;

/**
 * The FeatureVector is used to abstract away the storing of features.
 *
 * Before this interface was introduced in NlpTools there had to be a lot of
 * code that resembled the following:
 *   if (is_int(key($A)))
 *     $A = array_count_values($A);
 */
abstract class FeatureVector implements \ArrayAccess,
                                        \IteratorAggregate,
                                        \Countable
{
    /**
     * The FeatureVector is immutable. One can only read it.
     */
    final public function offsetSet($key, $value)
    {
        throw new \BadMethodCallException();
    }
    /**
     * The FeatureVector is immutable. One can only read it.
     */
    final public function offsetUnset($key)
    {
        throw new \BadMethodCallException();
    }
}
