<?php

namespace NlpTools\Documents;

class LinearChainDocumentSetTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleChain()
    {
        $doc = new TokensDocument(array());
        $tset = new LinearChainDocumentSet();
        $tset->addDocument("a", $doc);
        $tset->addDocument("b", $doc);
        $tset->addDocument("c", $doc);

        $this->assertEquals("a", $tset[0]->getClass());
        $this->assertEquals("a|b", $tset[1]->getClass());
        $this->assertEquals("b|c", $tset[2]->getClass());

        $tset = new LinearChainDocumentSet(0);
        $tset->addDocument("a", $doc);
        $tset->addDocument("b", $doc);
        $tset->addDocument("c", $doc);

        $this->assertEquals("a", $tset[0]->getClass());
        $this->assertEquals("b", $tset[1]->getClass());
        $this->assertEquals("c", $tset[2]->getClass());

        $tset = new LinearChainDocumentSet(2);
        $tset->addDocument("a", $doc);
        $tset->addDocument("b", $doc);
        $tset->addDocument("c", $doc);
        $tset->addDocument("d", $doc);

        $this->assertEquals("a", $tset[0]->getClass());
        $this->assertEquals("a|b", $tset[1]->getClass());
        $this->assertEquals("a|b|c", $tset[2]->getClass());
        $this->assertEquals("b|c|d", $tset[3]->getClass());

        $this->assertEquals(
            array(
                "a",
                "a|b",
                "a|b|c",
                "b|c|d"
            ),
            $tset->getClassSet()
        );
    }
}
