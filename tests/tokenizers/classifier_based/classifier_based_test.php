<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use \NlpTools\Tokenizers\ClassifierBasedTokenizer;
use \NlpTools\Tokenizers\WhitespaceTokenizer;
use \NlpTools\Classifiers\Classifier;
use \NlpTools\Documents\Document;

class EndOfSentence implements Classifier
{
	public function classify(array $classes, Document $d) {
		list($token,$before,$after) = $d->getDocumentData();
		
		$dotcnt = count(explode('.',$token))-1;
		$lastdot = substr($token,-1)=='.';
		
		if (!$lastdot) // assume that all sentences end in full stops
			return 'O';
		
		if ($dotcnt>1) // to catch some naive abbreviations U.S.A.
			return 'O';
		
		return 'EOW';
	}
}

$tok = new ClassifierBasedTokenizer(
	new EndOfSentence(),
	new WhitespaceTokenizer()
);

$text = "We are what we repeatedly do.
		Excellence, then, is not an act, but a habit.";

print_r($tok->tokenize($text));

// Array
// (
//    [0] => We are what we repeatedly do.
//    [1] => Excellence, then, is not an act, but a habit.
// )
