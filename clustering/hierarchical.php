<?php

namespace NlpTools\Clustering;

use NlpTools\Similarity\DistanceMatrix;
use NlpTools\Documents\TrainingSet;
use NlpTools\FeatureFactories\FeatureFactory;

/**
 * This class implements hierarchical agglomerative clustering.
 * It receives a DistanceMatrix instance as a parameter which
 * defines the strategy for the agglomeration (Single link,
 * Complete link, etc).
 */
class Hierarchical extends Clusterer
{
	protected $dm;

	public function __construct(DistanceMatrix $dm) {
		$this->dm = $dm;
	}

	public function cluster(TrainingSet $documents, FeatureFactory $ff) {
		// what a complete waste of memory here ...
		// the same data exists in $documents, $docs and
		// the only useful parts are in $this->dm
		$docs = $this->getDocumentArray($documents, $ff);
		$this->dm->initializeMatrix($docs);
		unset($docs); // perhaps save some memory

		// start with all the documents being in their
		// own cluster
		$clusters = range(0,count($documents)-1);
		$c = count($clusters);
		while ($c>1)
		{
			echo $c,PHP_EOL;
			// foreach cluster
			// compute its distance from any other cluster
			// using the distance matrix
			$mini = 0;
			$minj = 1;
			$mind = INF;
			for ($i=0;$i<$c;$i++)
			{
				for ($j=$i+1;$j<$c;$j++)
				{
					$d = $this->dm->dist(
						new \RecursiveIteratorIterator(
							new \RecursiveArrayIterator(
								array($clusters[$i])
							)
						),
						new \RecursiveIteratorIterator(
							new \RecursiveArrayIterator(
								array($clusters[$j])
							)
						)
					);
					if ($d < $mind)
					{
						$mini = $i;
						$minj = $j;
						$mind = $d;
					}
				}
			}
			// merge the two clusters with the smallest distance
			$clusters[$mini] = array($clusters[$mini],$clusters[$minj]);
			$c--;
			$clusters[$minj] = $clusters[$c];
			unset($clusters[$c]);
		}
		$clusters = array($clusters[0]);

		// return the dendrogram
		return array($clusters);
	}

	/**
	 * Flatten a dendrogram to an almost specific
	 * number of clusters (the closest power of 2 larger than
	 * $NC)
	 *
	 * @param array $tree The dendrogram to be flattened
	 * @param integer $NC The number of clusters to cut to
	 * @return array The flat clusters
	 */
	public static function dendrogramToClusters($tree,$NC) {
		$clusters = $tree;
		while (count($clusters)<$NC)
		{
			$tmpc = array();
			foreach ($clusters as $subclust)
			{
				if (!is_array($subclust))
					$tmpc[] = $subclust;
				else
				{
					foreach ($subclust as $c)
						$tmpc[] = $c;
				}
			}
			$clusters = $tmpc;
		}
		foreach ($clusters as &$c)
		{
			$c = iterator_to_array(
				new \RecursiveIteratorIterator(
					new \RecursiveArrayIterator(
						array($c)
					)
				),
				false // do not use keys
			);
		}
		return $clusters;
	}
}



