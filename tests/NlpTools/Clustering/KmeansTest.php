<?php

namespace NlpTools\Clustering;

use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\EuclideanPoint;
use NlpTools\Similarity\Euclidean;
use NlpTools\Clustering\CentroidFactories\Euclidean as EuclidCF;

class KmeansTest extends ClusteringTestBase
{

    protected function setUp()
    {
        if (!file_exists(TEST_DATA_DIR."/Clustering/KmeansTest")) {
            if (!file_exists(TEST_DATA_DIR."/Clustering"))
                mkdir(TEST_DATA_DIR."/Clustering");
            mkdir(TEST_DATA_DIR."/Clustering/KmeansTest");
        }
    }

    public function testEuclideanClustering()
    {
        $clust = new KMeans(
            2,
            new Euclidean(),
            new EuclidCF(),
            0.001
        );

        $tset = new TrainingSet();
        for ($i=0;$i<500;$i++) {
            $tset->addDocument(
                'A',
                EuclideanPoint::getRandomPointAround(100,100,45)
            );
        }
        for ($i=0;$i<500;$i++) {
            $tset->addDocument(
                'B',
                EuclideanPoint::getRandomPointAround(200,100,45)
            );
        }

        list($clusters,$centroids,$distances) = $clust->cluster($tset,new DataAsFeatures());

        $im = $this->drawClusters(
            $tset,
            $clusters,
            $centroids,
            false // lines or not
        );

        if ($im)
            imagepng($im,TEST_DATA_DIR."/Clustering/KmeansTest/clusters.png");

        // since the dataset is artificial and clearly separated, the kmeans
        // algorithm should always cluster it correctly
        foreach ($clusters as $clust) {
            $classes = array();
            foreach ($clust as $point_idx) {
                $class = $tset[$point_idx]->getClass();
                if (!isset($classes[$class]))
                    $classes[$class] = true;
            }
            // assert that all the documents (points) in this cluster belong
            // in the same class
            $this->assertCount(
                1,
                $classes
            );
        }
    }
}
