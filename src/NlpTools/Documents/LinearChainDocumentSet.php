<?php

namespace NlpTools\Documents;

/**
 * LinearChainDocumentSet is used to implement linear chain CRFs or HMMs in a
 * way that already implemented algorithms can be used. For instance using this
 * instead of a simple TrainingSet with a maxent model you can train a linear
 * chain CRF. The class passed to the feature factory is the class sequence
 * separated with |.
 */
class LinearChainDocumentSet extends TrainingSet
{
    protected $chainLength;

    /**
     * @param int $chainLength The number of previous labels to include in the class
     */
    public function __construct($chainLength=1)
    {
        parent::__construct();

        $this->chainLength = $chainLength;
    }

    /**
     * @return int The chain length
     */
    public function getChainLength()
    {
        return $this->chainLength;
    }

    /**
     * Create the class by calculating the class sequence and pass the creation
     * of the TrainingDocument to the parent class.
     */
    public function addDocument($class, DocumentInterface $doc)
    {
        if (strpos($class,"|")!==false)
            throw new \RuntimeException("In LinearChainDocumentSet the class cannot contain the pipe character");

        if (count($this)==0)
            return parent::addDocument($class, $doc);

        $classChain = explode("|",$this[count($this)-1]->getClass());
        $classChain[] = $class;
        if (count($classChain)<=$this->chainLength+1) {
            return parent::addDocument(implode("|", $classChain), $doc);
        }

        return parent::addDocument(
            implode(
                "|",
                array_slice($classChain, 1)
            ),
            $doc
        );
    }
}
