<?php

namespace NlpTools\FeatureFactories;

use \NlpTools\Documents\Document;

class DataAsFeatures implements FeatureFactory
{
	/**
	 * For use with TokensDocument mostly. Simply return the data as
	 * features. Could contain duplicates (a feature firing twice in
	 * for a signle document).
	 * 
	 * @param string $class The class for which we are calculating features
	 * @param Document $d The document to calculate features for.
	 * @return array
	 */
	public function getFeatureArray($class, Document $d) {
		return $d->getDocumentData();
	}
}

?>
