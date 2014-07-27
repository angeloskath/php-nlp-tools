<?php

namespace NlpTools\Clustering;

use NlpTools\Documents\EuclideanPoint;
use NlpTools\Documents\TrainingSet;
use NlpTools\Similarity\Neighbors\NaiveLinearSearch;
use NlpTools\Similarity\Euclidean;
use NlpTools\FeatureFactories\DataAsFeatures;

class DbscanTest extends ClusteringTestBase
{
    protected function setUp()
    {
        if (!file_exists(TEST_DATA_DIR."/Clustering/DbscanTest")) {
            if (!file_exists(TEST_DATA_DIR."/Clustering"))
                mkdir(TEST_DATA_DIR."/Clustering");
            mkdir(TEST_DATA_DIR."/Clustering/DbscanTest");
        }
    }

    /**
     * @dataProvider providePointsForTest
     */
    public function testDbscan($minPts,$eps,$points)
    {
        $tset = new TrainingSet();
        $points = array_chunk($points, 3);
        foreach ($points as $p) {
            $tset->addDocument(
                $p[0],
                new EuclideanPoint($p[1],$p[2])
            );
        }

        $dbscan = new Dbscan(
            $minPts,        // minimum points that constitute a cluster
            $eps,           // radius in which we should be looking for neighbors
            new Euclidean() // the distance metric to use
        );

        list($clusters, $noise) = $dbscan->cluster($tset, new DataAsFeatures());
        foreach ($noise as $idx) {
            $this->assertEquals(
                "N",
                $tset[$idx]->getClass()
            );
        }

        foreach ($clusters as $c) {
            $clust = array();
            foreach ($c as $idx) {
                if (!isset($clust[$tset[$idx]->getClass()])) {
                    $clust[$tset[$idx]->getClass()] = true;
                }
            }
            $this->assertCount(
                1,
                $clust
            );
        }
    }

    public function providePointsForTest()
    {
        return array(
            array(
                2, 1.1, array(
                    "A",0,0,
                    "A",0,1,
                    "A",0,2,
                    "B",2,0,
                    "B",2,1,
                    "B",2,2,
                    "N",4,0
                )
            )
        );
    }

    public function testDbscanWithRandomPoints()
    {
        $tset = new TrainingSet();
        for ($i=0;$i<150;$i++) {
            $tset->addDocument(
                '',
                EuclideanPoint::getRandomPointAround(150,100, 90)
            );
        }

        // the clusterer below should put everything into one cluster
        $clust = new Dbscan(
            150,             // minPts
            255,            // e neighborhood
            new Euclidean() // distance metric
        );
        list($clusters, $noise) = $clust->cluster($tset, new DataAsFeatures());
        $this->assertCount(
            0,
            $noise
        );
        $this->assertCount(
            count($tset),
            $clusters[0]
        );

        $clust = new Dbscan(
            4,
            20,
            new Euclidean()
        );
        list($clusters, $noise) = $clust->cluster($tset, new DataAsFeatures());

        $clusters[] = $noise;

        $im = $this->drawClusters(
            $tset,
            $clusters,
            null,
            false,
            5
        );
        if ($im)
            imagepng($im,TEST_DATA_DIR."/Clustering/DbscanTest/clusters.png");
    }

    public function testWithCircles()
    {
        $tset = new TrainingSet();
        for ($i=0.0;$i<2*M_PI;$i+=M_PI/20) {
            $tset->addDocument(
                'A',
                EuclideanPoint::getRandomPointAround(
                    150+70*cos($i),
                    100+70*sin($i),
                    8
                )
            );
            $tset->addDocument(
                'A',
                EuclideanPoint::getRandomPointAround(
                    150+70*cos($i),
                    100+70*sin($i),
                    8
                )
            );
        }
        for ($i=0.0;$i<2*M_PI;$i+=M_PI/10) {
            $tset->addDocument(
                'B',
                EuclideanPoint::getRandomPointAround(
                    150+20*cos($i),
                    100+20*sin($i),
                    8
                )
            );
            $tset->addDocument(
                'B',
                EuclideanPoint::getRandomPointAround(
                    150+20*cos($i),
                    100+20*sin($i),
                    8
                )
            );
        }

        $clust = new Dbscan(
            4,
            20,
            new Euclidean()
        );

        list($clusters,$noise) = $clust->cluster($tset, new DataAsFeatures());

        $this->assertCount(0,$noise);
        foreach ($clusters as $c) {
            $cl = array();
            foreach ($c as $i) {
                if (!isset($cl[$tset[$i]->getClass()])) {
                    $cl[$tset[$i]->getClass()] = true;
                }
            }
            $this->assertCount(1,$cl);
        }

        $clusters[] = $noise;
        $im = $this->drawClusters(
            $tset,
            $clusters,
            null,
            false,
            5
        );
        if ($im)
            imagepng($im,TEST_DATA_DIR."/Clustering/DbscanTest/circles.png");
    }
}
