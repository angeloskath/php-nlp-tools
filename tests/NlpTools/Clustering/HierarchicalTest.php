<?php

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
    protected function setUp()
    {
        if (!file_exists(TEST_DATA_DIR."/Clustering/HierarchicalTest")) {
            if (!file_exists(TEST_DATA_DIR."/Clustering"))
                mkdir(TEST_DATA_DIR."/Clustering");
            mkdir(TEST_DATA_DIR."/Clustering/HierarchicalTest");
        }
    }

    public function testSingleLink()
    {
        $docs = array(
            array('x'=>0,'y'=>0),
            array('x'=>0,'y'=>1),
            array('x'=>1,'y'=>3),
            array('x'=>4,'y'=>6),
            array('x'=>6,'y'=>6)
        );

        $sl = new SingleLink();
        $sl->initializeStrategy(new Euclidean(), $docs);

        $pair = $sl->getNextMerge();
        $this->assertEquals(
            array(0,1),
            $pair
        );

        $pair = $sl->getNextMerge();
        $this->assertEquals(
            array(3,4),
            $pair
        );

        $pair = $sl->getNextMerge();
        $this->assertEquals(
            array(0,2),
            $pair
        );

        $pair = $sl->getNextMerge();
        $this->assertEquals(
            array(0,3),
            $pair
        );

        $this->setExpectedException(
            "RuntimeException",
            "Can't extract from an empty heap"
        );
        $sl->getNextMerge();
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
    public function testCompleteLink()
    {
        $docs = array(
            array('x'=>0,'y'=>1),
            array('x'=>1,'y'=>1),
            array('x'=>2,'y'=>1),
            array('x'=>3,'y'=>1),
            array('x'=>4,'y'=>1),
            array('x'=>7,'y'=>1)
        );

        $cl = new CompleteLink();
        $cl->initializeStrategy(new Euclidean(), $docs);

        $pair = $cl->getNextMerge();
        $this->assertEquals(
            array(0,1),
            $pair
        );

        $pair = $cl->getNextMerge();
        $this->assertEquals(
            array(2,3),
            $pair
        );

        $pair = $cl->getNextMerge();
        $this->assertEquals(
            array(2,4),
            $pair
        );

        $pair = $cl->getNextMerge();
        $this->assertEquals(
            array(0,2),
            $pair
        );

        $pair = $cl->getNextMerge();
        $this->assertEquals(
            array(0,5),
            $pair
        );

        $this->setExpectedException(
            "RuntimeException",
            "Can't extract from an empty heap"
        );
        $cl->getNextMerge();
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
    public function testGroupAverage()
    {
        $docs = array(
            array('x'=>0,'y'=>1),
            array('x'=>1,'y'=>1),
            array('x'=>2,'y'=>1),
            array('x'=>3,'y'=>1),
            array('x'=>4.51,'y'=>1),
        );

        $ga = new GroupAverage();
        $ga->initializeStrategy(new Euclidean(), $docs);

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(0,1),
            $pair
        );

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(2,3),
            $pair
        );

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(0,2),
            $pair
        );

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(0,4),
            $pair
        );

        $docs[4] = array('x'=>4.49,'y'=>1);
        $ga->initializeStrategy(new Euclidean(), $docs);

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(0,1),
            $pair
        );

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(2,3),
            $pair
        );

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(2,4),
            $pair
        );

        $pair = $ga->getNextMerge();
        $this->assertEquals(
            array(0,2),
            $pair
        );
    }

    public function testDendrogramToClusters()
    {
        $dendrograms = array(
            array(
                array(array(0,1),array(array(2,3),4)),
                array(array(0,1),array(2,3,4))
            ),
            array(
                array(array(0,array(1,array(2,array(3,array(4,array(5,array(6,7)))))))),
                array(array(0),array(1),array(2),array(3,4,5,6,7))
            )
        );

        foreach ($dendrograms as $i=>$d) {
            $this->assertEquals(
                $d[1],
                Hierarchical::dendrogramToClusters(
                    $d[0],
                    count($d[1])
                ),
                "Error transforming dendrogram $i"
            );
        }
    }

    public function testClustering1()
    {
        $points = array(
            array('x'=>1, 'y'=>1),
            array('x'=>1, 'y'=>2),
            array('x'=>2, 'y'=>2),
            array('x'=>3, 'y'=>3),
            array('x'=>3, 'y'=>4),
        );

        $tset = new TrainingSet();
        foreach ($points as $p)
            $tset->addDocument('',new TokensDocument($p));

        $hc = new Hierarchical(
            new SingleLink(), // use the single link strategy
            new Euclidean() // with euclidean distance
        );

        list($dendrogram) = $hc->cluster($tset,new DataAsFeatures());
        $this->assertEquals(
            array(
                array(
                    array(
                        array(
                            0,
                            1
                        ),
                        2
                    ),
                    array(
                        3,
                        4
                    )
                )
            ),
            $dendrogram
        );
    }

    public function testClustering2()
    {
        $N = 50;
        $tset = new TrainingSet();
        for ($i=0;$i<$N;$i++) {
            $tset->addDocument(
                '',
                EuclideanPoint::getRandomPointAround(100,100,45)
            );
        }
        for ($i=0;$i<$N;$i++) {
            $tset->addDocument(
                '',
                EuclideanPoint::getRandomPointAround(200,100,45)
            );
        }

        $hc = new Hierarchical(
            new SingleLink(), // use the single link strategy
            new Euclidean() // with euclidean distance
        );

        list($dendrogram) = $hc->cluster($tset,new DataAsFeatures());
        $dg = $this->drawDendrogram(
            $tset,
            $dendrogram,
            600 // width
        );

        $clusters = Hierarchical::dendrogramToClusters($dendrogram,2);
        $im = $this->drawClusters(
            $tset,
            $clusters,
            null, // no centroids
            false, // no lines
            10 // emphasize points (for little points)
        );

        if ($dg)
            imagepng($dg, TEST_DATA_DIR."/Clustering/HierarchicalTest/dendrogram.png");
        if ($im)
            imagepng($im, TEST_DATA_DIR."/Clustering/HierarchicalTest/clusters.png");
    }
}
