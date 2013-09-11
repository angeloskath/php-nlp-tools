<?php

/**
 * To use this example code you will need:
 *
 * 1. The external gradient descent optimizer which
 *    is at https://github.com/angeloskath/nlp-maxent-optimizer/
 *    Do not forget to set the environment variable.
 *
 * 2. The imdb review dataset assembled by Pang/Lee
 *    found at http://www.cs.cornell.edu/people/pabo/movie-review-data/
 *
 * 3. A way to split and shuffle the files. Suggested (90% split):
 *    for f in `ls pos`; do echo `pwd`/pos/$f; done >>/tmp/imdb.list
 *    for f in `ls neg`; do echo `pwd`/neg/$f; done >>/tmp/imdb.list
 *    shuf /tmp/imdb.list >/tmp/imdb-shuffled.list
 *    head -n 1800 /tmp/imdb-shuffled.list > train
 *    tail -n 200 /tmp/imdb-shuffled.list > test
 *
 * Then call the script like this:
 *    php -d memory_limit=300M sentiment_maxent.php train test
 *
 */

// include the autoloader
include '../autoloader.php';

use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\FeatureFactories\FunctionFeatures;
use NlpTools\Documents\Document;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Optimizers\ExternalMaxentOptimizer;
use NlpTools\Models\Maxent;
use NlpTools\Classifiers\FeatureBasedLinearClassifier;

// create needed reusable objects, a tokenizer and a feature factory
$tok = new WhitespaceTokenizer();
$ff = new FunctionFeatures();
$ff->add(function ($class, DocumentInterface $d) {
    $r = array();
    foreach ($d->getDocumentData() as $tok)
        $r[] = $class.$tok;

    return $r;
});

// create
//  1. an empty training set
//  2. an optimizer
//  3. an empty model
$tset = new TrainingSet();
$OPTIMIZER_PATH = isset($_ENV["GD_OPTIMIZER"]) ? $_ENV["GD_OPTIMIZER"] : 'gradient-descent';
$optimizer = new ExternalMaxentOptimizer($OPTIMIZER_PATH);
$model = new Maxent(array());

// argv[1] and argv[2] are paths to files that contain the paths
// to the actual documents.
$train = new SplFileObject($argv[1]);
$test = new SplFileObject($argv[2]);

// fill in the training set
foreach ($train as $f) {
    $f = substr($f,0,-1);
    if (strlen($f)==0)
        continue;
    $class = "neg";
    if (strpos($f,"pos")!==false) {
        $class = "pos";
    }
    $tset->addDocument(
            $class,
            new TokensDocument($tok->tokenize(file_get_contents($f)))
        );
}

// train the model
$model->train($ff,$tset,$optimizer);

// to use the model we need a classifier
$cls = new FeatureBasedLinearClassifier($ff,$model);

// evaluate the model
$correct = 0;
$total = 0;
foreach ($test as $f) {
    $f = substr($f,0,-1);
    if (strlen($f)==0)
        continue;
    $class = "neg";
    if (strpos($f,"pos")!==false) {
        $class = "pos";
    }
    $doc = new TokensDocument($tok->tokenize(file_get_contents($f)));
    $predicted = $cls->classify(array("pos","neg"),$doc);
    if ($predicted == $class) {
        $correct++;
    }
    $total++;
}

printf("Acc: %.2f%%\n",(100*$correct/$total));
