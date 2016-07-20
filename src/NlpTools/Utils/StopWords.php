<?php

namespace NlpTools\Utils;

/**
 * Stop Words are words which are filtered out because they carry
 * little to no information.
 *
 * This class transforms tokens. If they are listed as stop words
 * it returns null in order for the Document to remove them.
 * Otherwise it leaves them unchanged.
 */
class StopWords implements TransformationInterface
{
    protected $stopwords;
    protected $inner_transform;

    public function __construct(array $stopwords, TransformationInterface $transform = null)
    {
        $this->stopwords = array_fill_keys(
            $stopwords,
            true
        );

        $this->inner_transform = $transform;
    }

    public function transform($token)
    {
        $tocheck = $token;

        if ($this->inner_transform) {
            $tocheck = $this->inner_transform->transform($token);
        }

        return isset($this->stopwords[$tocheck]) ? null : $token;
    }
}
