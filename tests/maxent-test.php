<?php

include('../autoloader.php');

use NlpTools\Documents\Document;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\WordDocument;
use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Models\Maxent;
use NlpTools\Optimizers\MaxentGradientDescent;
use NlpTools\Classifiers\FeatureBasedLinearClassifier;

$tokens = array();
$classes = array();
foreach (file('dev-doc') as $line)
{
	$tmp = explode(' ',substr($line,0,-1));
	$tokens[] = $tmp[0];
	$classes[] = $tmp[1];
}

$feats = new FunctionFeatures();
//$feats->add(function ($class,Document $d) {
//	return $class.current($d->getDocumentData());
//});
$feats->add(function ($class,Document $d) {
	if ($class!='START_SENTENCE') return;
	$dat = $d->getDocumentData();
	$prev = $dat[1];
	end($prev);
	return 'prev='.current($prev);
});
$feats->add(function ($class,Document $d) {
	if ($class!='START_SENTENCE') return;
	$w = current($d->getDocumentData());
	if (ctype_upper($w[0]))
		return "isCapitalized";
});

$s = new TrainingSet();
foreach ($tokens as $index=>$token)
{
	$s->addDocument($classes[$index],new WordDocument($tokens,$index,5));
}

$maxent = new Maxent(array());
$maxent->train($feats, $s,new MaxentGradientDescent(0.01,1,100000));

$maxent->dumpWeights();

$true_positives = 0;
$false_neg = 0;
$false_pos = 0;

$classifier = new FeatureBasedLinearClassifier($feats, $maxent);
$s->setAsKey(TrainingSet::CLASS_AS_KEY);
foreach ($s as $class=>$doc)
{
	$predicted_class = $classifier->classify(array('O','START_SENTENCE'),$doc);
	if ($class!=$predicted_class)
	{
		if ($predicted_class=='O')
		{
			$false_neg++;
		}
		else
		{
			$false_pos++;
		}
	}
	else
	{
		$true_positives++;
	}
}

$precision = function () use($true_positives,$false_pos) { return $true_positives/($true_positives+$false_pos); };
$recall = function () use($true_positives,$false_neg) { return $true_positives/($true_positives+$false_neg); };
$f1 = function () use($precision,$recall) { return (2*$precision()*$recall())/($precision()+$recall()); };

printf("Precision:\t%.3f\nRecall:\t\t%.3f\nF1:\t\t%.3f\n", $precision(), $recall(), $f1());

?>
