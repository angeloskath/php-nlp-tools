<?php

declare(strict_types=1);

namespace NlpTools\Clustering;

use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\EuclideanPoint;
use NlpTools\Similarity\Euclidean;
use NlpTools\Clustering\CentroidFactories\Euclidean as EuclidCF;
use PHPUnit\Framework\Attributes\Group;

class KmeansTest extends ClusteringTestBase
{
    protected function setUp(): void
    {
        if (!file_exists(TEST_DATA_DIR . "/Clustering/KmeansTest")) {
            if (!file_exists(TEST_DATA_DIR . "/Clustering")) {
                mkdir(TEST_DATA_DIR . "/Clustering");
            }

            mkdir(TEST_DATA_DIR . "/Clustering/KmeansTest");
        }
    }

    #[Group('Slow')]
    public function testEuclideanClustering(): void
    {
        $kMeans = new KMeans(
            2,
            new Euclidean(),
            new EuclidCF(),
            0.001
        );

        $trainingSet = new TrainingSet();
        for ($i = 0; $i < 500; $i++) {
            $trainingSet->addDocument(
                'A',
                EuclideanPoint::getRandomPointAround(100, 100, 45)
            );
        }

        for ($i = 0; $i < 500; $i++) {
            $trainingSet->addDocument(
                'B',
                EuclideanPoint::getRandomPointAround(200, 100, 45)
            );
        }

        [$clusters, $centroids, $distances] = $kMeans->cluster($trainingSet, new DataAsFeatures());

        $im = $this->drawClusters(
            $trainingSet,
            $clusters,
            $centroids,
            false // lines or not
        );

        if ($im !== null && $im !== false) {
            imagepng($im, TEST_DATA_DIR . "/Clustering/KmeansTest/clusters.png");
        }

        // since the dataset is artificial and clearly separated, the kmeans
        // algorithm should always cluster it correctly
        foreach ($clusters as $cluster) {
            $classes = [];
            foreach ($cluster as $point_idx) {
                $class = $trainingSet[$point_idx]->getClass();
                if (!isset($classes[$class])) {
                    $classes[$class] = true;
                }
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
