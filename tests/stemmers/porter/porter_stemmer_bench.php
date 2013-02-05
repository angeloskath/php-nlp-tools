<?php

/*
 * words.txt and stems.txt are taken from
 * http://tartarus.org/~martin/PorterStemmer/
 * 
 * The other porter stemmer implementation is the one mentioned in
 * http://tartarus.org/~martin/PorterStemmer/ as a php implementation
 * */

include ('../../../autoloader.php');
include('other_php_porter_stemmer.php');

use NlpTools\Stemmers\PorterStemmer as OurPorterStemmer;

$stemmer = new OurPorterStemmer();
$wordlist = file('words.txt',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

$start = microtime(true);
foreach ($wordlist as $word)
{
	$stemmer->stem($word);
}
$dur1 = microtime(true)-$start;

$start = microtime(true);
foreach ($wordlist as $word)
{
	PorterStemmer::Stem($word);
}
$dur2 = microtime(true)-$start;

echo $dur1,PHP_EOL;
echo $dur2,PHP_EOL;

$speedup = $dur1/$dur2;
if ($speedup > 1)
{
	printf("NlpTools implementation is %.2f%% slower\n", 100*(1-1/$speedup));
}
else
{
	printf("NlpTools implementation is %.2f%% faster\n", 100*(1-$speedup));
}

?>
