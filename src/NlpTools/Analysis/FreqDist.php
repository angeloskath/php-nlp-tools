<?php

declare(strict_types=1);

namespace NlpTools\Analysis;

use NlpTools\Documents\TokensDocument;

/**
 * Extract the Frequency distribution of keywords
 * @author Dan Cardin
 */
class FreqDist
{
    /**
     * An associative array that holds all the frequencies per token
     *
     * @var array<string, float>
     */
    protected array $keyValues = [];

    /**
     * The total number of tokens originally passed into FreqDist
     */
    protected int $totalTokens;

    /**
     * This sorts the token meta data collection right away so use
     * frequency distribution data can be extracted.
     *
     * @param array<int, string> $tokens
     */
    public function __construct(array $tokens)
    {
        $this->preCompute($tokens);
        $this->totalTokens = count($tokens);
    }

    /**
     * Get the total number of tokens in this tokensDocument
     */
    public function getTotalTokens(): int
    {
        return $this->totalTokens;
    }

    /**
     * Internal function for summarizing all the data into a key value store
     *
     * @param array<int, string> $tokens
     */
    protected function preCompute(array &$tokens): void
    {
        // count all the tokens up and put them in a key value store
        $this->keyValues = array_count_values($tokens);
        arsort($this->keyValues);
    }

    /**
     * Return the weight of a single token
     */
    public function getWeightPerToken(): float
    {
        return 1 / $this->getTotalTokens();
    }

    /**
     * Return get the total number of unique tokens
     */
    public function getTotalUniqueTokens(): int
    {
        return count($this->keyValues);
    }

    /**
     * Return the sorted keys by frequency desc
     *
     * @return array<int, string>
     */
    public function getKeys(): array
    {
        return array_keys($this->keyValues);
    }

    /**
     * Return the sorted values by frequency desc
     *
     * @return array<int, float>
     */
    public function getValues(): array
    {
        return array_values($this->keyValues);
    }

    /**
     * Return the full key value store
     *
     * @return array<string, float>
     */
    public function getKeyValues(): array
    {
        return $this->keyValues;
    }

    /**
     * Return a token's count
     */
    public function getTotalByToken(string $string): float|false
    {
        $array = $this->keyValues;
        if (array_key_exists($string, $array)) {
            return $array[$string];
        }

        return false;
    }

    /**
     * Return a token's weight (for user's own tf-idf/pdf/iduf implem)
     */
    public function getTokenWeight(string $string): float|false
    {
        if ($this->getTotalByToken($string)) {
            return $this->getTotalByToken($string) / $this->getTotalTokens();
        }

        return false;
    }

    /**
     * Returns an array of tokens that occurred once
     * @todo This is an inefficient approach
     *
     * @return array<int, string>
     */
    public function getHapaxes(): array
    {
        $samples = [];
        foreach ($this->getKeyValues() as $sample => $count) {
            if ((int) $count === 1) {
                $samples[] = $sample;
            }
        }

        return $samples;
    }
}
