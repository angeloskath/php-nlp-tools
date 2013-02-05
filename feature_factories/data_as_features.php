<?php

namespace NlpTools\FeatureFactories;

use \NlpTools\Documents\Document;

class DataAsFeatures implements FeatureFactory
{
	/*
	 * For use with TokensDocument mostly. Simply return the data as
	 * features. Could contain duplicates (a feature firing twice in
	 * for a signle document).
	 * 
	 * name: getFeatureArray
	 * @return array
	 */
	public function getFeatureArray($class, Document $d) {
		return $d->getDocumentData();
	}
}

?>
