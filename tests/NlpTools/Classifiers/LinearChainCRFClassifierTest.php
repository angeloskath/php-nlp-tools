<?php

namespace NlpTools\Classifiers;

use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\LinearChainDocumentSet;
use NlpTools\Models\Maxent;
use NlpTools\Optimizers\MaxentGradientDescent;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\FeatureFactories\MaxentFeatures;
use NlpTools\FeatureFactories\LinearChainCRFFeatures;

/**
 * Construct a small sequence that will give a different result if trained as a
 * linear chain crf compared to a maxent model (the maximum won't be found by
 * the greedy algorithm).
 */
class LinearChainCRFClassifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Train the models on the following sequence (capitals are labels)
     *    a a b a a a a b a b a
     *    A A B A A A A B A A A
     */
    public function testSequenceModeling()
    {
        $optimizer = new MaxentGradientDescent(0.1, 0.1);
        $crf = new Maxent(array());
        $tset = new TrainingSet();
        $lcset = new LinearChainDocumentSet();
        $a = new TokensDocument(array("a"));
        $b = new TokensDocument(array("b"));

        $tset->addDocument("A", $a);
        $tset->addDocument("A", $a);
        $tset->addDocument("B", $b);
        $tset->addDocument("A", $a);
        $tset->addDocument("A", $a);
        $tset->addDocument("A", $a);
        $tset->addDocument("A", $a);
        $tset->addDocument("B", $b);
        $tset->addDocument("A", $a);
        $tset->addDocument("A", $b);
        $tset->addDocument("A", $a);

        $lcset->addDocument("A", $a);
        $lcset->addDocument("A", $a);
        $lcset->addDocument("B", $b);
        $lcset->addDocument("A", $a);
        $lcset->addDocument("A", $a);
        $lcset->addDocument("A", $a);
        $lcset->addDocument("A", $a);
        $lcset->addDocument("B", $b);
        $lcset->addDocument("A", $a);
        $lcset->addDocument("A", $b);
        $lcset->addDocument("A", $a);

        $crf->train(
            new LinearChainCRFFeatures(new DataAsFeatures()),
            $lcset,
            $optimizer
        );

        $this->assertTrue(
            $crf->getWeight("A|A") > $crf->getWeight("A|B")
        );
        $this->assertTrue(
            $crf->getWeight("A|A") > 0
        );

        $cls = new LinearChainCRFClassifier(
            new LinearChainCRFFeatures(new DataAsFeatures()),
            $crf
        );

        $seq = $cls->classify(
            array("A","B"),
            $tset // can also pass lcset, just showcasing that it is not required
        );
        // A|A transition is so common that the best guess for the sequence is all A.
        $this->assertEquals(
            array("A","A","A","A","A","A","A","A","A","A","A"),
            $seq
        );
    }
}
