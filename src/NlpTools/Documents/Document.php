<?php

namespace NlpTools\Documents;

/**
 * A Document is a representation of a Document to be classified.
 * It can be a representation of a word, of a bunch of text, of a text
 * that has structure (ex.: Title,Body,Link)
 */
interface Document
{
	/**
	 * Return the data of what is being represented. If it were a word
	 * we could return a word. If it were a blog post we could return
	 * an array(Title,Body,array(Comments)).
	 * 
	 * @return mixed
	 */
	public function getDocumentData();
}

?>
