<?php

declare(strict_types=1);

namespace NlpTools\Analysis;

use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use PHPUnit\Framework\TestCase;

class IdfTest extends TestCase
{
    public function testIdf(): void
    {
        $trainingSet = new TrainingSet();
        $trainingSet->addDocument(
            "",
            new TokensDocument(["a", "b", "c", "d"])
        );
        $trainingSet->addDocument(
            "",
            new TokensDocument(["a", "c", "d"])
        );
        $trainingSet->addDocument(
            "",
            new TokensDocument(["a"])
        );

        $idf = new Idf($trainingSet);

        $this->assertEquals(
            0.405,
            round($idf["c"], 3),
        );
        $this->assertEquals(
            1.099,
            round($idf["b"], 3),
        );
        $this->assertEquals(
            1.099,
            round($idf["non-existing"], 3),
        );
        $this->assertEquals(
            0,
            $idf["a"]
        );
    }
}
