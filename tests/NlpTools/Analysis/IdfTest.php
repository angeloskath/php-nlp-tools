<?php

namespace NlpTools\Analysis;

use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;

class IdfTest extends \PHPUnit_Framework_TestCase
{
    public function testIdf()
    {
        $ts = new TrainingSet();
        $ts->addDocument(
            "",
            new TokensDocument(array("a","b","c","d"))
        );
        $ts->addDocument(
            "",
            new TokensDocument(array("a","c","d"))
        );
        $ts->addDocument(
            "",
            new TokensDocument(array("a"))
        );

        $idf = new Idf($ts);

        $this->assertEquals(
            0.405,
            $idf->idf("c"),
            null,
            0.001
        );
        $this->assertEquals(
            1.098,
            $idf->idf("b"),
            null,
            0.001
        );
        $this->assertEquals(
            1.098,
            $idf->idf("non-existing"),
            null,
            0.001
        );
        $this->assertEquals(
            0,
            $idf->idf("a")
        );

        $this->assertEquals(
            3,
            $idf->numberofDocuments()
        );

        $this->assertEquals(
            3,
            $idf->termFrequency("a")
        );

        $this->assertEquals(
            1,
            $idf->documentFrequency("b")
        );

        $this->assertEquals(
            8,
            $idf->numberofCollectionTokens()
        );
    }
}
