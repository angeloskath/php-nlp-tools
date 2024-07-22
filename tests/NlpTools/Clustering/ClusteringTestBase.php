<?php

declare(strict_types=1);

namespace NlpTools\Clustering;

use PHPUnit\Framework\TestCase;
use NlpTools\Documents\TrainingSet;

class ClusteringTestBase extends TestCase
{
    /**
     * Return a color distributed in the pallete according to $t
     * $t should be in (0,1)
     *
     * @return array<int, mixed>
     */
    protected function getColor(float $t): array
    {
        $u = fn($x): int => ($x > 0) ? 1 : 0;
        $pulse = fn($x, $a, $b): int => $u($x - $a) - $u($x - $b);

        return [(int) ( 255 * ( $pulse($t, 0, 1 / 3) + $pulse($t, 1 / 3, 2 / 3) * (2 - 3 * $t) ) ), (int) ( 255 * ( $pulse($t, 0, 1 / 3) * 3 * $t + $pulse($t, 1 / 3, 2 / 3) + $pulse($t, 2 / 3, 1) * (3 - 3 * $t) ) ), (int) ( 255 * ( $pulse($t, 1 / 3, 2 / 3) * (3 * $t - 1) + $pulse($t, 2 / 3, 1) ) )];
    }

    /**
     * Return a gd handle with a visualization of the clustering or null in case gd is not present.
     *
     * @param array<int, mixed> $clusters
     * @param array<int, mixed>|null $centroids
     */
    protected function drawClusters(TrainingSet $trainingSet, array $clusters, ?array $centroids = null, bool $lines = false, int $emphasize = 0, int $w = 300, int $h = 200): mixed
    {
        if (!function_exists('imagecreate')) {
            return null;
        }

        $im = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($im, 255, 255, 255);
        $colors = [];
        $numberOfClusters = count($clusters);
        for ($i = 1; $i <= $numberOfClusters; $i++) {
            [$r, $g, $b] = $this->getColor($i / $numberOfClusters);
            $colors[] = imagecolorallocate($im, $r, $g, $b);
        }

        imagefill($im, 0, 0, $white);
        foreach ($clusters as $cid => $cluster) {
            foreach ($cluster as $idx) {
                $data = $trainingSet[$idx]->getDocumentData();
                if ($emphasize > 0) {
                    imagefilledarc($im, $data['x'], $data['y'], $emphasize, $emphasize, 0, 360, $colors[$cid], 0);
                } else {
                    imagesetpixel($im, $data['x'], $data['y'], $colors[$cid]);
                }
            }

            if (is_array($centroids)) {
                $x = $centroids[$cid]['x'];
                $y = $centroids[$cid]['y'];
                if ($lines) {
                    // draw line
                    // for cosine similarity
                    imagesetthickness($im, 5);
                    imageline($im, 0, 0, $x * 400, $y * 400, $colors[$cid]);
                } else {
                    // draw circle for euclidean
                    imagefilledarc($im, (int) $x, (int) $y, 10, 10, 0, 360, $colors[$cid], 0);
                }
            }
        }

        return $im;
    }

    /**
     * Return a gd handle with a visualization of the given dendrogram or null
     * if gd is not present.
     *
     * @param array<int, mixed> $dendrogram
     */
    protected function drawDendrogram(TrainingSet $trainingSet, array $dendrogram, int $w = 300, int $h = 200): mixed
    {
        if (!function_exists('imagecreate')) {
            return null;
        }

        $im = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $blue = imagecolorallocate($im, 0, 0, 255);
        imagefill($im, 0, 0, $white);

        // padding 5%
        $padding = round(0.05 * $w);
        // equally distribute
        $d = ($w - 2 * $padding) / count($trainingSet);
        $count_depth = function ($a) use (&$count_depth): int|float {
            if (is_array($a)) {
                return max(
                    array_map(
                        $count_depth,
                        $a
                    )
                ) + 1;
            }

            return 1;
        };
        $depth = $count_depth($dendrogram) - 1;
        $d_v = ($h - 2 * $padding) / $depth;

        // offset from bottom
        $y = $h - $padding;
        $left = $padding;

        $draw_subcluster = function ($dendrogram, &$left) use (&$im, $d, $y, $d_v, $black, &$draw_subcluster, $blue): array {
            if (!is_array($dendrogram)) {
                imagestring($im, 1, (int) ($left - (2 * strlen((string) $dendrogram))), (int) $y, (string) $dendrogram, $black);
                $left += $d;

                return [$left - $d, $y - 5];
            }

            [$l, $yl] = $draw_subcluster($dendrogram[0], $left);
            [$r, $yr] = $draw_subcluster($dendrogram[1], $left);
            $ym = min($yl, $yr) - $d_v;
            imageline($im, (int) $l, (int) $yl, (int) $l, (int) $ym, $blue);
            imageline($im, (int) $r, (int) $yr, (int) $r, (int) $ym, $blue);
            imageline($im, (int) $l, (int) $ym, (int) $r, (int) $ym, $blue);

            return [$l + ($r - $l) / 2, $ym];
        };

        if (count($dendrogram) === 1) {
            $draw_subcluster($dendrogram[0], $left);
        } else {
            $draw_subcluster($dendrogram, $left);
        }

        return $im;
    }
}
