<?php

declare(strict_types=1);

namespace NlpTools\Clustering;

use NlpTools\Clustering\MergeStrategies\SingleLink;
use NlpTools\Clustering\MergeStrategies\CompleteLink;
use NlpTools\Clustering\MergeStrategies\GroupAverage;
use NlpTools\Similarity\Euclidean;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\EuclideanPoint;
use NlpTools\FeatureFactories\DataAsFeatures;

class HierarchicalTest extends ClusteringTestBase
{
    protected function setUp(): void
    {
        if (!file_exists(TEST_DATA_DIR . "/Clustering/HierarchicalTest")) {
            if (!file_exists(TEST_DATA_DIR . "/Clustering")) {
                mkdir(TEST_DATA_DIR . "/Clustering");
            }

            mkdir(TEST_DATA_DIR . "/Clustering/HierarchicalTest");
        }
    }

    public function testSingleLink(): void
    {
        $docs = [['x' => 0, 'y' => 0], ['x' => 0, 'y' => 1], ['x' => 1, 'y' => 3], ['x' => 4, 'y' => 6], ['x' => 6, 'y' => 6]];

        $singleLink = new SingleLink();
        $singleLink->initializeStrategy(new Euclidean(), $docs);

        $pair = $singleLink->getNextMerge();
        $this->assertEquals(
            [0, 1],
            $pair
        );

        $pair = $singleLink->getNextMerge();
        $this->assertEquals(
            [3, 4],
            $pair
        );

        $pair = $singleLink->getNextMerge();
        $this->assertEquals(
            [0, 2],
            $pair
        );

        $pair = $singleLink->getNextMerge();
        $this->assertEquals(
            [0, 3],
            $pair
        );

        $this->expectException(\RuntimeException::class);
        $singleLink->getNextMerge();
    }

    /**
     * We are clustering the following points.
     *
     *  1 | * * * * *     *
     *  0 +----------------
     * -1 | 0 1 2 3 4     7
     *
     * They are merged with the following order (x coordinates indicate which point).
     *
     *     +-----+
     *     |     |
     *  +----+   |
     *  |    |   |
     *  |   +--+ |
     *  |   |  | |
     *  |  +-+ | |
     *  |  | | | |
     * +-+ | | | |
     * | | | | | |
     * 0 1 2 3 4 7
     *
     */
    public function testCompleteLink(): void
    {
        $docs = [['x' => 0, 'y' => 1], ['x' => 1, 'y' => 1], ['x' => 2, 'y' => 1], ['x' => 3, 'y' => 1], ['x' => 4, 'y' => 1], ['x' => 7, 'y' => 1]];

        $completeLink = new CompleteLink();
        $completeLink->initializeStrategy(new Euclidean(), $docs);

        $pair = $completeLink->getNextMerge();
        $this->assertEquals(
            [0, 1],
            $pair
        );

        $pair = $completeLink->getNextMerge();
        $this->assertEquals(
            [2, 3],
            $pair
        );

        $pair = $completeLink->getNextMerge();
        $this->assertEquals(
            [2, 4],
            $pair
        );

        $pair = $completeLink->getNextMerge();
        $this->assertEquals(
            [0, 2],
            $pair
        );

        $pair = $completeLink->getNextMerge();
        $this->assertEquals(
            [0, 5],
            $pair
        );

        $this->expectException(\RuntimeException::class);
        $completeLink->getNextMerge();
    }

    /**
     *
     * | * * * *   *
     * +------------
     *   0 1 2 3   4.51
     *
     * results in
     *
     *    +----+
     *    |    |
     *  +---+  |
     *  |   |  |
     *  |  +-+ |
     * +-+ | | |
     * | | | | |
     * 0 1 2 3 4.51
     *
     * while
     *
     * | * * * *   *
     * +------------
     *   0 1 2 3   4.49
     *
     * in
     *
     *  +----+
     *  |    |
     *  |   +--+
     *  |   |  |
     *  |  +-+ |
     * +-+ | | |
     * | | | | |
     * 0 1 2 3 4.49
     *
     * because the distance between the groups {0,1}-{2,3} is 2 and {2,3},{4.5} is also 2.
     *
     */
    public function testGroupAverage(): void
    {
        $docs = [['x' => 0, 'y' => 1], ['x' => 1, 'y' => 1], ['x' => 2, 'y' => 1], ['x' => 3, 'y' => 1], ['x' => 4.51, 'y' => 1]];

        $groupAverage = new GroupAverage();
        $groupAverage->initializeStrategy(new Euclidean(), $docs);

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [0, 1],
            $pair
        );

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [2, 3],
            $pair
        );

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [0, 2],
            $pair
        );

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [0, 4],
            $pair
        );

        $docs[4] = ['x' => 4.49, 'y' => 1];
        $groupAverage->initializeStrategy(new Euclidean(), $docs);

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [0, 1],
            $pair
        );

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [2, 3],
            $pair
        );

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [2, 4],
            $pair
        );

        $pair = $groupAverage->getNextMerge();
        $this->assertEquals(
            [0, 2],
            $pair
        );
    }

    public function testDendrogramToClusters(): void
    {
        $dendrograms = [[[[0, 1], [[2, 3], 4]], [[0, 1], [2, 3, 4]]], [[[0, [1, [2, [3, [4, [5, [6, 7]]]]]]]], [[0], [1], [2], [3, 4, 5, 6, 7]]]];

        foreach ($dendrograms as $i => $d) {
            $this->assertEquals(
                $d[1],
                Hierarchical::dendrogramToClusters(
                    $d[0],
                    count($d[1])
                ),
                'Error transforming dendrogram ' . $i
            );
        }
    }

    public function testClustering1(): void
    {
        $points = [['x' => 1, 'y' => 1], ['x' => 1, 'y' => 2], ['x' => 2, 'y' => 2], ['x' => 3, 'y' => 3], ['x' => 3, 'y' => 4]];

        $trainingSet = new TrainingSet();

        foreach ($points as $point) {
            $trainingSet->addDocument('', new TokensDocument($point));
        }

        $hierarchical = new Hierarchical(
            new SingleLink(), // use the single link strategy
            new Euclidean() // with euclidean distance
        );

        [$dendrogram] = $hierarchical->cluster($trainingSet, new DataAsFeatures());
        $this->assertEquals(
            [[[[0, 1], 2], [3, 4]]],
            $dendrogram
        );
    }

    public function testClustering2(): void
    {
        $N = 50;
        $trainingSet = new TrainingSet();
        for ($i = 0; $i < $N; $i++) {
            $trainingSet->addDocument(
                '',
                EuclideanPoint::getRandomPointAround(100, 100, 45)
            );
        }

        for ($i = 0; $i < $N; $i++) {
            $trainingSet->addDocument(
                '',
                EuclideanPoint::getRandomPointAround(200, 100, 45)
            );
        }

        $hierarchical = new Hierarchical(
            new SingleLink(), // use the single link strategy
            new Euclidean() // with euclidean distance
        );

        [$dendrogram] = $hierarchical->cluster($trainingSet, new DataAsFeatures());
        $dg = $this->drawDendrogram(
            $trainingSet,
            $dendrogram,
            600 // width
        );

        $clusters = Hierarchical::dendrogramToClusters($dendrogram, 2);
        $im = $this->drawClusters(
            $trainingSet,
            $clusters,
            null, // no centroids
            false, // no lines
            10 // emphasize points (for little points)
        );

        if ($dg !== null) {
            imagepng($dg, TEST_DATA_DIR . "/Clustering/HierarchicalTest/dendrogram.png");
        }

        if ($im !== null) {
            imagepng($im, TEST_DATA_DIR . "/Clustering/HierarchicalTest/clusters.png");
        }

        // should have proper assertions at some point
        $this->assertTrue(true);
    }
}
