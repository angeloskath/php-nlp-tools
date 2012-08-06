<?php

include('../autoloader.php');

$text = file_get_contents('token-test');
$tokenizer = new NlpTools\WhitespaceTokenizer();
$tokens = $tokenizer->tokenize($text);

$feats = new NlpTools\FunctionFeatures();
$feats->add(function ($class,NlpTools\Document $d) {
	return current($d->getDocumentData());
});
$feats->add(function ($class,NlpTools\Document $d) {
	$w = current($d->getDocumentData());
	if (ctype_upper($w[0]))
		return "isCapitalized";
});

$documents = array();
foreach ($tokens as $index=>$token)
{
	$documents[$index] = new NlpTools\WordDocument($tokens,$index,5);
}

foreach ($documents as $d)
{
	echo '['.implode(',',$feats->getFeatureArray('0',$d)).']',PHP_EOL;
}

?>
