<?php

include ('../../autoloader.php');
include ('../testing.php');

use NlpTools\Documents\WordDocument;

$tokens = array("The","quick","brown","fox","jumped","over","the","lazy","dog");

// test if it selects the ith token correctly
foreach ($tokens as $i=>$t) {
	$doc = new WordDocument($tokens, $i, 0);
	list($w,$prev,$next) = $doc->getDocumentData();
	_assert(
		$w===$t,
		"The {$i}th token should be $t not $w"
	);
	_assert(
		empty($prev),
		"No context means \$prev should be empty"
	);
	_assert(
		empty($next),
		"No context means \$next should be empty"
	);
}

// test prev context
for ($i=0;$i<5;$i++)
{
	$doc = new WordDocument($tokens, 4, $i);
	list($_,$prev,$_) = $doc->getDocumentData();
	_assert(
		count($prev)==$i,
		"With $i words context prev should be $i words long"
	);
	for (
		$j=3,$y=$i-1;
		$j>=4-$i;
		$y--,$j--) {
		_assert(
			$prev[$y]==$tokens[$j],
			"Words in prev do not match the words in the tokens array"
		);
	}
}

// test next context
for ($i=0;$i<5;$i++)
{
	$doc = new WordDocument($tokens, 4, $i);
	list($_,$_,$next) = $doc->getDocumentData();
	_assert(
		count($next)==$i,
		"With $i words context next should be $i words long"
	);
	for ($j=5; $j<5+$i; $j++) {
		_assert(
			$next[$j-5]==$tokens[$j],
			"Words in next do not match the words in the tokens array"
		);
	}
}

// TODO: check behaviour near the token array's edges
