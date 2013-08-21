<?php

namespace NlpTools\Clustering;

class ClusteringTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * Return a color distributed in the pallete according to $t
     * $t should be in (0,1)
     */
    protected function getColor($t)
    {
        $u = function ($x) { return ($x>0) ? 1 : 0; };
        $pulse = function ($x,$a,$b) use ($u) { return $u($x-$a)-$u($x-$b); };

        return array(
            (int) ( 255*( $pulse($t,0,1/3) + $pulse($t,1/3,2/3)*(2-3*$t) ) ),
            (int) ( 255*( $pulse($t,0,1/3)*3*$t + $pulse($t,1/3,2/3) + $pulse($t,2/3,1)*(3-3*$t) ) ),
            (int) ( 255*( $pulse($t,1/3,2/3)*(3*$t-1) + $pulse($t,2/3,1) ) )
        );
    }

    /**
     * Return a gd handle with a visualization of the clustering or null in case gd is not present.
     */
    protected function drawClusters($tset, $clusters, $centroids=null, $lines=False,$emphasize=0,$w=300,$h=200)
    {
        if (!function_exists('imagecreate'))
            return null;

        $im = imagecreatetruecolor($w,$h);
        $white = imagecolorallocate($im,255,255,255);
        $colors = array();
        $NC = count($clusters);
        for ($i=1;$i<=$NC;$i++) {
            list($r,$g,$b) = $this->getColor($i/$NC);
            $colors[] = imagecolorallocate($im,$r,$g,$b);
        }

        imagefill($im,0,0,$white);
        foreach ($clusters as $cid=>$cluster) {
            foreach ($cluster as $idx) {
                $data = $tset[$idx]->getDocumentData();
                if ($emphasize>0)
                    imagefilledarc($im,$data['x'],$data['y'],$emphasize,$emphasize,0,360,$colors[$cid],0);
                else
                    imagesetpixel($im,$data['x'],$data['y'],$colors[$cid]);
            }
            if (is_array($centroids)) {
                $x = $centroids[$cid]['x'];
                $y = $centroids[$cid]['y'];
                if ($lines) {
                    // draw line
                    // for cosine similarity
                    imagesetthickness($im,5);
                    imageline($im,0,0,$x*400,$y*400,$colors[$cid]);
                } else {
                    // draw circle for euclidean
                    imagefilledarc($im,$x,$y,10,10,0,360,$colors[$cid],0);
                }
            }
        }

        return $im;
    }

    /**
     * Return a gd handle with a visualization of the given dendrogram or null
     * if gd is not present.
     */
    protected function drawDendrogram($tset, $dendrogram, $w=300, $h=200)
    {
        if (!function_exists('imagecreate'))
            return null;

        $im = imagecreatetruecolor($w,$h);
        $white = imagecolorallocate($im, 255,255,255);
        $black = imagecolorallocate($im, 0,0,0);
        $blue = imagecolorallocate($im, 0,0,255);
        imagefill($im, 0,0, $white);

        // padding 5%
        $padding = round(0.05*$w);
        // equally distribute
        $d = ($w-2*$padding)/count($tset);
        $count_depth = function ($a) use (&$depth, &$count_depth) {
            if (is_array($a)) {
                return max(
                    array_map(
                        $count_depth,
                        $a
                    )
                ) + 1;
            } else {
                return 1;
            }
        };
        $depth = $count_depth($dendrogram)-1;
        $d_v = ($h-2*$padding)/$depth;

        // offset from bottom
        $y = $h-$padding;
        $left = $padding;

        $draw_subcluster = function ($dendrogram, &$left) use (&$im, $d, $y, $d_v, $black, &$draw_subcluster,$blue) {
            if (!is_array($dendrogram)) {
                imagestring($im, 1, $left-(2 * strlen($dendrogram)), $y, $dendrogram, $black);
                $left += $d;

                return array($left - $d,$y-5);
            }
            list($l,$yl) = $draw_subcluster($dendrogram[0],$left);
            list($r,$yr) = $draw_subcluster($dendrogram[1],$left);
            $ym = min($yl,$yr)-$d_v;
            imageline($im, $l, $yl, $l, $ym, $blue);
            imageline($im, $r, $yr, $r, $ym, $blue);
            imageline($im, $l, $ym, $r, $ym, $blue);

            return array($l+($r-$l)/2,$ym);
        };

        if (count($dendrogram)==1)
            $draw_subcluster($dendrogram[0],$left);
        else
            $draw_subcluster($dendrogram,$left);

        return $im;
    }
}
