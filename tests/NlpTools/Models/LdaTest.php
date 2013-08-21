<?php

namespace NlpTools\Models;

use NlpTools\Random\Distributions\Dirichlet;
use NlpTools\Random\Generators\MersenneTwister;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\TokensDocument;
use NlpTools\FeatureFactories\DataAsFeatures;

/**
 * Functional testing of the Latent Dirichlet Allocation
 * (LDA) model
 *
 * To check the output see the results in the tests/data/Models/LdaTest/results
 * folder.
 */
class LdaTest extends \PHPUnit_Framework_Testcase
{
    protected $path;
    protected $tset;
    protected $topics;

    protected function setUp()
    {
        if (!extension_loaded("gd")) {
            $this->markTestSkipped("The gd library is not available");
        }

        $this->path = TEST_DATA_DIR."/Models/LdaTest";
        if (!file_exists($this->path)) {
            if (!file_exists(TEST_DATA_DIR."/Models"))
                mkdir(TEST_DATA_DIR."/Models");
            mkdir($this->path);
        }

        if (!file_exists("{$this->path}/topics")) {
            mkdir("{$this->path}/topics");
        }
        $this->createTopics();

        if (!file_exists("{$this->path}/data")) {
            mkdir("{$this->path}/data");
        }
        if (count(new \DirectoryIterator("{$this->path}/data"))<502) {
            $this->createData();
        }

        if (!file_exists("{$this->path}/results")) {
            mkdir("{$this->path}/results");
        }

        $this->loadData();
    }

    /**
     * @group Slow
     * @group VerySlow
     */
    public function testLda()
    {
        $lda = new Lda(
            new DataAsFeatures(), // feature factory
            10,                   // number of topics
            1,                    // dirichlet prior per doc topic dist
            1                     // dirichlet prior per word topic dist
        );

        $this->assertInstanceOf(
            "NlpTools\Models\Lda",
            $lda
        );

        $docs = $lda->generateDocs($this->tset);
        $this->assertCount(
            count($this->tset),
            $docs
        );

        $lda->initialize($docs);

        for ($i=0;$i<100;$i++) {
            $lda->gibbsSample($docs);
            $topics = $lda->getPhi();
            echo $lda->getLogLikelihood(),PHP_EOL;
            foreach ($topics as $t=>$topic) {
                $name = sprintf("{$this->path}/results/topic-%04d-%04d",$i,$t);
                $max = max($topic);
                $this->createImage(
                    array_map(
                        function ($x) use ($topic,$max) {
                            return array_map(
                                function ($y) use ($x,$topic,$max) {
                                    return (int) (($topic[$y*5+$x]/$max)*255);
                                },
                                range(0,4)
                            );
                        },
                        range(0,4)
                    ),
                    $name
                );
            }
        }

        // TODO: assert the resemblance of the inferred topics
        //       with the actual topics
    }

    // WARNING: Massive set up code follows
    // Lda is one of the hardest models to test.
    // This functional test is the test the creators of Lda
    // performed themselves.
    //
    // TODO: Unit testing for lda is needed

    protected function createTopics()
    {
        $topics = array(
            array(
                array(1,1,1,1,1),
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(0,0,0,0,0)
            ),
            array(
                array(0,0,0,0,0),
                array(1,1,1,1,1),
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(0,0,0,0,0)
            ),
            array(
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(1,1,1,1,1),
                array(0,0,0,0,0),
                array(0,0,0,0,0)
            ),
            array(
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(1,1,1,1,1),
                array(0,0,0,0,0)
            ),
            array(
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(0,0,0,0,0),
                array(1,1,1,1,1)
            ),
            array(
                array(0,0,0,0,1),
                array(0,0,0,0,1),
                array(0,0,0,0,1),
                array(0,0,0,0,1),
                array(0,0,0,0,1)
            ),
            array(
                array(0,0,0,1,0),
                array(0,0,0,1,0),
                array(0,0,0,1,0),
                array(0,0,0,1,0),
                array(0,0,0,1,0)
            ),
            array(
                array(0,0,1,0,0),
                array(0,0,1,0,0),
                array(0,0,1,0,0),
                array(0,0,1,0,0),
                array(0,0,1,0,0)
            ),
            array(
                array(0,1,0,0,0),
                array(0,1,0,0,0),
                array(0,1,0,0,0),
                array(0,1,0,0,0),
                array(0,1,0,0,0)
            ),
            array(
                array(1,0,0,0,0),
                array(1,0,0,0,0),
                array(1,0,0,0,0),
                array(1,0,0,0,0),
                array(1,0,0,0,0)
            )
        );

        $this->topics = array_map(
            function ($topic) {
                $t = call_user_func_array(
                    "array_merge",
                    $topic
                );

                $s = array_sum($t);

                return array_map(
                    function ($ti) use ($s) {
                        return $ti/$s;
                    },
                    $t
                );
            },
            $topics
        );

        // multiply by 255 to make gray-scale images of
        // the above arrays
        $topics = array_map(
            function ($topic) {
                return array_map(
                    function ($row) {
                        return array_map(
                            function ($pixel) {
                                return (int) (255*$pixel);
                            },
                            $row
                        );
                    },
                    $topic
                );
            },
            $topics
        );

        // save them to disk
        foreach ($topics as $key=>$topic) {
            $this->createImage($topic, "{$this->path}/topics/topic-$key");
        }
    }

    protected function createData()
    {
        $dir = new Dirichlet(1, count($this->topics));

        for ($i=0;$i<500;$i++) {
            $d = $this->createDocument($this->topics, $dir->sample(), 100);
            $this->createImage($d, "{$this->path}/data/$i");
        }
    }

    protected function loadData()
    {
        $this->tset = new TrainingSet();
        foreach (new \DirectoryIterator("{$this->path}/data") as $f) {
            if ($f->isDir())
                continue;

            $this->tset->addDocument(
                "",
                new TokensDocument(
                    $this->fromImg($f->getRealPath())
                )
            );
        }
    }

    /**
     * Save a two dimensional array as a grey-scale image
     */
    protected function createImage(array $img,$filename)
    {
        $im = imagecreate(count($img),count(current($img)));
        imagecolorallocate($im,0,0,0);
        foreach ($img as $y=>$row) {
            foreach ($row as $x=>$color) {
                $color = min(255,max(0,$color));
                $c = imagecolorallocate($im,$color,$color,$color);
                imagesetpixel($im,$x,$y,$c);
            }
        }
        imagepng($im,$filename);
    }

    /**
     * Draw once from a multinomial distribution
     */
    protected function draw($d)
    {
        $mt = MersenneTwister::get(); // simply mt_rand but in the interval [0,1)
        $x = $mt->generate();
        $p = 0.0;
        foreach ($d as $i=>$v) {
            $p+=$v;
            if ($p > $x)
                return $i;
        }
    }

    /**
     * Create a document sticking to the model's assumptions
     * and hypotheses
     */
    public function createDocument($topic_dists,$theta,$length)
    {
        $doc = array_fill_keys(range(0,24),0);
        while ($length-- > 0) {
            $topic = $this->draw($theta);
            $word = $this->draw($topic_dists[$topic]);
            $doc[$word] += 1;
        }

        return array_map(
            function ($start) use ($doc) {
                return array_slice($doc,$start,5);
            },
            range(0,24,5)
        );
    }

    /**
     * Load a document from an image saved to disk
     */
    public function fromImg($file)
    {
        $im = imagecreatefrompng($file);
        $d = array();
        for ($w=0;$w<25;$w++) {
            $x = (int) ($w%5);
            $y = (int) ($w/5);

            $c = imagecolorsforindex($im,imagecolorat($im,$x,$y));
            $c = $c['red'];
            if ($c>0) {
                $d = array_merge(
                    $d,
                    array_fill_keys(
                        range(0,$c-1),
                        $w
                    )
                );
            }
        }

        return $d;
    }

}
