<?php

namespace NlpTools\Clustering\MergeStrategies;

use NlpTools\Similarity\Distance;

interface MergeStrategy
{
	/**
	 * Study the docs and preprocess anything required for
	 * computing the merges
	 */
	public function initializeStrategy(Distance $d, array &$docs);

	/**
	 * Return the next two clusters for merging and assume
	 * they are merged (ex. update a similarity matrix)
	 *
	 * @return array An array with two numbers which are the cluster ids
	 */
	public function getNextMerge();
}

