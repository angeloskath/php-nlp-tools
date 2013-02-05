<?php

/*
 * words.txt and stems.txt are taken from
 * http://tartarus.org/~martin/PorterStemmer/
 * */

include ('../../../autoloader.php');

$words = fopen('words.txt','r');
$stems = fopen('stems.txt','r');

use NlpTools\Stemmers\PorterStemmer;

$stemmer = new PorterStemmer();

while (!feof($words))
{
	
	$word = substr(fgets($words),0,-1);
	$stem = substr(fgets($stems),0,-1);
	$our_stem = $stemmer->stem($word);
	
	if ($our_stem!==$stem)
	{
		echo $word,', stem: ',$stem,' our_stem: ',$our_stem,PHP_EOL;
	}
}

?>
