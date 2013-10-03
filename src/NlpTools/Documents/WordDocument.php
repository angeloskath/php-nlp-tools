<?php

namespace NlpTools\Documents;
use NlpTools\Utils\Interfaces\TokenTransformationInterface;

/**
 * A Document that represents a single word but with a context of a
 * larger document. Useful for Named Entity Recognition
 */
class WordDocument implements DocumentInterface
{
    protected $word;
    protected $before;
    protected $after;
    public function __construct(array $tokens, $index, $context)
    {
        $this->word = $tokens[$index];

        $this->before = array();
        for ($start = max($index-$context,0);$start<$index;$start++) {
            $this->before[] = $tokens[$start];
        }

        $this->after = array();
        $end = min($index+$context+1,count($tokens));
        for ($start = $index+1;$start<$end;$start++) {
            $this->after[] = $tokens[$start];
        }
    }

    /**
     * It returns an array with the first element being the actual word,
     * the second element being an array of previous words, and the
     * third an array of following words
     *
     * @return array
     */
    public function getDocumentData()
    {
        return array($this->word,$this->before,$this->after);
    }

    /**
     * Apply the transformations to the token and the context tokens too
     * @param TokenTransformationInterface $transformer 
     */
    public function applyTransformation(TokenTransformationInterface $transformer)
    {        
        $this->word = $transformer->transform($this->word);
        
        array_walk($this->before, function(&$beforeWord, $index, $transformerPassedIn) { 
            $beforeWord = $transformerPassedIn->transform($beforeWord); 
        }, $transformer);
        
        // filter and re-index the array
        $this->before = array_values(array_filter($this->before));
        
        array_walk($this->after, function(&$afterWord, $index, $transformerPassedIn) { 
            $afterWord = $transformerPassedIn->transform($afterWord); 
        }, $transformer);        
        
        //filter and re-index the array
        $this->after = array_values(array_filter($this->after));
                        
    }
}
