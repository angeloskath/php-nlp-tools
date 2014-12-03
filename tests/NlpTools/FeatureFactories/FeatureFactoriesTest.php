<?php

namespace NlpTools\FeatureFactories;

use NlpTools\Documents\TokensDocument;

class FeatureFactoriesTest extends \PHPUnit_Framework_TestCase
{
    public function testDataAsFeatures()
    {
        $ff = new DataAsFeatures();

        $features = $ff->getFeatureArray(
            "",
            new TokensDocument(array("a", "a", "b"))
        );

        $this->assertInstanceOf(
            'NlpTools\\FeatureVector\\FeatureVector',
            $features
        );

        $this->assertEquals(2, $features["a"]);
        $this->assertEquals(1, $features["b"]);
        $this->assertFalse(isset($features["c"]));
    }

    public function testFunctionFeatures()
    {
        // The first function is effectively the same as DataAsFeatures
        $ff = new FunctionFeatures(array(
            function ($class, $doc) {
                return $doc->getDocumentData();
            },
            function ($class, $doc) {
                $upper = array();
                foreach ($doc->getDocumentData() as $token) {
                    if (ctype_upper($token[0]))
                        $upper[] = "{UPPERCASE}";
                }

                return $upper;
            }
        ));

        $ff->modelFrequency();
        $features = $ff->getFeatureArray(
            "",
            new TokensDocument(array("A", "A", "b"))
        );

        $this->assertInstanceOf(
            'NlpTools\\FeatureVector\\FeatureVector',
            $features
        );

        $this->assertEquals(2, $features["A"]);
        $this->assertEquals(2, $features["{UPPERCASE}"]);
        $this->assertEquals(1, $features["b"]);
        $this->assertFalse(isset($features["c"]));

        $ff->modelPresence();
        $features = $ff->getFeatureArray(
            "",
            new TokensDocument(array("A", "A", "b"))
        );

        $this->assertInstanceOf(
            'NlpTools\\FeatureVector\\FeatureVector',
            $features
        );

        $this->assertEquals(1, $features["A"]);
        $this->assertEquals(1, $features["{UPPERCASE}"]);
        $this->assertEquals(1, $features["b"]);
        $this->assertFalse(isset($features["c"]));
    }

    public function testMaxentFeatures()
    {
        $ff = new MaxentFeatures(new DataAsFeatures());

        $features = $ff->getFeatureArray(
            "c1",
            new TokensDocument(array("a", "a", "b"))
        );

        $this->assertInstanceOf(
            'NlpTools\\FeatureVector\\FeatureVector',
            $features
        );

        $this->assertFalse(isset($features["a"]));
        $this->assertFalse(isset($features["b"]));
        $this->assertEquals(2, $features["c1 ^ a"]);
        $this->assertEquals(1, $features["c1 ^ b"]);
    }

    public function testLinearChainCRFFeatures()
    {
        $ff = new LinearChainCRFFeatures(
            new DataAsFeatures(),
            new DataAsFeatures()
        );

        $features = $ff->getFeatureArray(
            "c1",
            new TokensDocument(array("a", "a", "b"))
        );
        $this->assertInstanceOf(
            'NlpTools\\FeatureVector\\FeatureVector',
            $features
        );
        $this->assertEquals(
            array(
                "c1"=>1,
                "c1 ^ a"=>2,
                "c1 ^ b"=>1
            ),
            iterator_to_array($features)
        );

        $features = $ff->getFeatureArray(
            "c1|c1",
            new TokensDocument(array("a", "a", "b"))
        );
        $this->assertInstanceOf(
            'NlpTools\\FeatureVector\\FeatureVector',
            $features
        );
        $this->assertEquals(
            array(
                "c1|c1"=>1,
                "c1 ^ a"=>2,
                "c1 ^ b"=>1,
                "c1|c1 ^ a"=>2,
                "c1|c1 ^ b"=>1
            ),
            iterator_to_array($features)
        );
    }
}
