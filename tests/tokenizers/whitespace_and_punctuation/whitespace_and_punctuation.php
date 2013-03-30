<?php

include ('../../../autoloader.php');
include ('../../testing.php');

use \NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;

function arrays_match($a1,$a2) {
	return count(array_diff($a1,$a2))==0;
}

$tok = new WhitespaceAndPunctuationTokenizer();

$s = "This is a simple space delimited string
with new lines and many     spaces between the words.
Also	tabs	tabs	tabs	tabs";
$tokens = array('This','is','a','simple','space','delimited','string','with','new','lines','and','many','spaces','between','the','words','.','Also','tabs','tabs','tabs','tabs');

_assert(arrays_match($tok->tokenize($s),$tokens),"Problem tokenizing simple ASCII whitespace with ascii content");


$s = "Ελληνικό κείμενο για παράδειγμα utf-8 χαρακτήρων";
$tokens = array('Ελληνικό','κείμενο','για','παράδειγμα','utf','-','8','χαρακτήρων');

_assert(arrays_match($tok->tokenize($s),$tokens),"Problem tokenizing simple ASCII whitespace with utf-8 content");


$s = "Here exists non-breaking space   ";
$tokens = array('Here','exists','non','-','breaking','space');

_assert(arrays_match($tok->tokenize($s),$tokens),"Problem tokenizing utf-8 whitespace");
