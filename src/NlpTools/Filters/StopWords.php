<?php
namespace NlpTools\Filters;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;
use \InvalidArgumentException;

/**
 * Stop words allows a developer to provide a list that get filtered
 * from the data set
 * @author Dan Cardin (yooper)
 */
class StopWords implements TokenTransformationInterface
{
    /**
     * An array of stop words
     * @var array
     */
    protected $stopWords = null;

    /**
     * load in the stop words, an associative array is created from the array
     * that uses the stop word as the key and sets a value of true
     * @param array $stopWords an array word list
     */
    public function __construct(array $stopWords)
    {
        $this->stopWords = array_fill_keys($stopWords, true);
    }

    /**
     * Checks if the token exists in the stop word lit
     * @param string|null $token
     */
    public function transform($token)
    {
        return isset($this->stopWords[$token]) ? null : $token;
    }

}
