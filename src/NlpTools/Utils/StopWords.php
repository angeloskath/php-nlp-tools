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

    public function __construct(array $stopwords)
    {
        $this->stopwords = array_fill_keys(
            $stopwords,
            true
        );
    }

    public function transform($token)
    {
        return isset($this->stopwords[$token]) ? null : $token;
    }
}
