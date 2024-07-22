<?php

declare(strict_types=1);

namespace NlpTools\Models;

use NlpTools\Random\Distributions\Dirichlet;
use NlpTools\Random\Generators\MersenneTwister;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\TokensDocument;
use NlpTools\FeatureFactories\DataAsFeatures;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Functional testing of the Latent Dirichlet Allocation
 * (LDA) model
 *
 * To check the output see the results in the tests/data/Models/LdaTest/results
 * folder.
 */
class LdaTest extends TestCase
{
    protected string $path;

    protected TrainingSet $tset;

    /**
     * @var array<int, mixed>
     */
    protected array $topics;

    protected function setUp(): void
    {
        if (!extension_loaded("gd")) {
            $this->markTestSkipped("The gd library is not available");
        }

        $this->path = TEST_DATA_DIR . "/Models/LdaTest";
        if (!file_exists($this->path)) {
            if (!file_exists(TEST_DATA_DIR . "/Models")) {
                mkdir(TEST_DATA_DIR . "/Models");
            }

            mkdir($this->path);
        }

        if (!file_exists($this->path . '/topics')) {
            mkdir($this->path . '/topics');
        }

        $this->createTopics();

        if (!file_exists($this->path . '/data')) {
            mkdir($this->path . '/data');
        }

        $fileCount = count(glob($this->path . '/data/*'));
        if ($fileCount < 502) {
            $this->createData();
        }

        if (!file_exists($this->path . '/results')) {
            mkdir($this->path . '/results');
        }

        $this->loadData();
    }

    #[Group('Slow')]
    #[Group('VerySlow')]
    public function testLda(): void
    {
        $lda = new Lda(
            new DataAsFeatures(), // feature factory
            10,                   // number of topics
            1,                    // dirichlet prior per doc topic dist
            1                     // dirichlet prior per word topic dist
        );

        $this->assertInstanceOf(
            \NlpTools\Models\Lda::class,
            $lda
        );

        $docs = $lda->generateDocs($this->tset);
        $this->assertCount(
            count($this->tset),
            $docs
        );

        $lda->initialize($docs);

        for ($i = 0; $i < 100; $i++) {
            $lda->gibbsSample($docs);
            $topics = $lda->getPhi();

            foreach ($topics as $t => $topic) {
                $name = sprintf($this->path . '/results/topic-%04d-%04d', $i, $t);
                $max = max($topic);
                $this->createImage(
                    array_map(
                        fn($x): array => array_map(
                            fn($y): int => (int) (($topic[$y * 5 + $x] / $max) * 255),
                            range(0, 4)
                        ),
                        range(0, 4)
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

    protected function createTopics(): void
    {
        $topics = [[[1, 1, 1, 1, 1], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0]], [[0, 0, 0, 0, 0], [1, 1, 1, 1, 1], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0]], [[0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [1, 1, 1, 1, 1], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0]], [[0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [1, 1, 1, 1, 1], [0, 0, 0, 0, 0]], [[0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [0, 0, 0, 0, 0], [1, 1, 1, 1, 1]], [[0, 0, 0, 0, 1], [0, 0, 0, 0, 1], [0, 0, 0, 0, 1], [0, 0, 0, 0, 1], [0, 0, 0, 0, 1]], [[0, 0, 0, 1, 0], [0, 0, 0, 1, 0], [0, 0, 0, 1, 0], [0, 0, 0, 1, 0], [0, 0, 0, 1, 0]], [[0, 0, 1, 0, 0], [0, 0, 1, 0, 0], [0, 0, 1, 0, 0], [0, 0, 1, 0, 0], [0, 0, 1, 0, 0]], [[0, 1, 0, 0, 0], [0, 1, 0, 0, 0], [0, 1, 0, 0, 0], [0, 1, 0, 0, 0], [0, 1, 0, 0, 0]], [[1, 0, 0, 0, 0], [1, 0, 0, 0, 0], [1, 0, 0, 0, 0], [1, 0, 0, 0, 0], [1, 0, 0, 0, 0]]];

        $this->topics = array_map(
            function ($topic): array {
                $t = array_merge(...$topic);

                $s = array_sum($t);

                return array_map(
                    fn($ti): int|float => $ti / $s,
                    $t
                );
            },
            $topics
        );

        // multiply by 255 to make gray-scale images of
        // the above arrays
        $topics = array_map(
            fn($topic): array => array_map(
                fn($row): array => array_map(
                    fn($pixel): int => (int) (255 * $pixel),
                    $row
                ),
                $topic
            ),
            $topics
        );

        // save them to disk
        foreach ($topics as $key => $topic) {
            $this->createImage($topic, sprintf('%s/topics/topic-%s', $this->path, $key));
        }
    }

    protected function createData(): void
    {
        $dirichlet = new Dirichlet(1, count($this->topics));

        for ($i = 0; $i < 500; $i++) {
            $d = $this->createDocument($this->topics, $dirichlet->sample(), 100);
            $this->createImage($d, sprintf('%s/data/%d', $this->path, $i));
        }
    }

    protected function loadData(): void
    {
        $this->tset = new TrainingSet();
        foreach (new \DirectoryIterator($this->path . '/data') as $f) {
            if ($f->isDir()) {
                continue;
            }

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
     *
     * @param array<int, mixed> $img
     */
    protected function createImage(array $img, string $filename): void
    {
        $im = imagecreate(count($img), count(current($img)));
        imagecolorallocate($im, 0, 0, 0);
        foreach ($img as $y => $row) {
            foreach ($row as $x => $color) {
                $color = min(255, max(0, $color));
                $c = imagecolorallocate($im, $color, $color, $color);
                imagesetpixel($im, $x, $y, $c);
            }
        }

        imagepng($im, $filename);
    }

    /**
     * Draw once from a multinomial distribution
     *
     * @param array<int, mixed> $d
     */
    protected function draw(array $d): ?int
    {
        $mersenneTwister = MersenneTwister::get(); // simply mt_rand but in the interval [0,1)
        $x = $mersenneTwister->generate();
        $p = 0.0;
        foreach ($d as $i => $v) {
            $p += $v;
            if ($p > $x) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Create a document sticking to the model's assumptions
     * and hypotheses
     *
     * @param array<int, mixed> $topicDists
     * @param array<int, mixed> $theta
     * @return array<int, mixed>
     */
    public function createDocument(array $topicDists, array $theta, int $length): array
    {
        $doc = array_fill_keys(range(0, 24), 0);
        while ($length-- > 0) {
            $topic = $this->draw($theta);
            $word = $this->draw($topicDists[$topic]);
            $doc[$word] += 1;
        }

        return array_map(
            fn($start): array => array_slice($doc, $start, 5),
            range(0, 24, 5)
        );
    }

    /**
     * Load a document from an image saved to disk
     *
     * @return array<int, mixed>
     */
    public function fromImg(string $file): array
    {
        $im = imagecreatefrompng($file);
        $d = [];
        for ($w = 0; $w < 25; $w++) {
            $x = $w % 5;
            $y = (int) ($w / 5);

            $c = imagecolorsforindex($im, imagecolorat($im, $x, $y));
            $c = $c['red'];
            if ($c > 0) {
                $d = array_merge(
                    $d,
                    array_fill_keys(
                        range(0, $c - 1),
                        $w
                    )
                );
            }
        }

        return $d;
    }
}
