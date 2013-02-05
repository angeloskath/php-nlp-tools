<?php

include('../autoloader.php');

use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Documents\Document;
use NlpTools\Documents\WordDocument;

$text = file_get_contents('token-test');
$tokenizer = new WhitespaceTokenizer();
$tokens = $tokenizer->tokenize($text);

$feats = new FunctionFeatures();
$feats->add(function ($class,Document $d) {
	return current($d->getDocumentData());
});
$feats->add(function ($class,Document $d) {
	$w = current($d->getDocumentData());
	if (ctype_upper($w[0]))
		return "isCapitalized";
});

$documents = array();
foreach ($tokens as $index=>$token)
{
	$documents[$index] = new WordDocument($tokens,$index,5);
}

foreach ($documents as $d)
{
	echo '['.implode(',',$feats->getFeatureArray('0',$d)).']',PHP_EOL;
}

?>
