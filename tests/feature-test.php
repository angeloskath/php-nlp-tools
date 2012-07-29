<?php

include('../autoloader.php');

$text = file_get_contents('test-doc');
$tokenizer = new NlpTools\WhitespaceTokenizer();
$tokens = $tokenizer->tokenize($text);

$feats = new NlpTools\FunctionFeatures();
$feats->add(function ($class, $tokens) {
	return current($tokens);
});
$feats->add(function ($class, $tokens) {
	$w = current($tokens);
	if (ctype_upper($w[0]))
		return "isCapitalized";
});

do
{
	echo current($tokens)." - [".implode(',',$feats->getFeatureArray('O',$tokens))."]\n";
} while (next($tokens))

?>
