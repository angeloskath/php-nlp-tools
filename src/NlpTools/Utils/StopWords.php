<?php

declare(strict_types=1);

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
    /**
     * @var array<string, mixed>
     */
    protected array $stopwords;

    /**
     * @param array<int, string> $stopwords
     */
    public function __construct(array $stopwords, protected ?TransformationInterface $transformation = null)
    {
        $this->stopwords = array_fill_keys(
            $stopwords,
            true
        );
    }

    public function transform(string $token): ?string
    {
        $tocheck = $token;

        if ($this->transformation instanceof TransformationInterface) {
            $tocheck = $this->transformation->transform($token);
        }

        return isset($this->stopwords[$tocheck]) ? null : $token;
    }
}
