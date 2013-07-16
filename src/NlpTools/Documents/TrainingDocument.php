<?php

namespace NlpTools\Documents;

/**
 * A TrainingDocument is a document that "decorates" any other document
 * to add the real class of the document. It is used while training
 * together with the training set.
 */
class TrainingDocument implements Document
{
	protected $d;
	protected $class;
	
	/**
	 * @param string $class The actual class of the Document $d
	 * @param Document $d The document to be decorated
	 */
	public function __construct($class, Document $d) {
		$this->d = $d;
		$this->class = $class;
	}
	public function getDocumentData() {
		return $this->d->getDocumentData();
	}
	public function getClass() {
		return $this->class;
	}
}

?>
