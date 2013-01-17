PHP NlpTools
=============

NlpTools is a set of php 5.3+ classes for beginner to
semi advanced natural language processing work.

Contents
---------

NlpTools now contain

* Tokenizers
  1. WhitespaceTokenizer
  2. WhitespaceAndPunctuationTokenizer
  3. ClassifierBasedTokenizer (This tokenizer allows us to build a lot
  more complex tokenizers than the previous ones)

* Models
  1. Multinomial Naive Bayes
  2. Maximum Entropy (Conditional Exponential model)
  3. TODO: Many more should be scheduled for the future
     (SVM, perceptron)

* Optimizers (MaxEnt only)
  1. A gradient descent optimizer (written in php) for educational use.
     It is a simple implementation for anyone wanting to know a bit
     more about either GD or MaxEnt models
  2. A fast (faster than nltk-scipy), parallel gradient descent
     optimizer written in [Go](http://golang.org/). This optimizer
     resides in another repo, it is used via the external optimizer
     which could be used with any other optimizer
     like [MEGAM](http://www.cs.utah.edu/~hal/megam/)
  

A bit of Documentation/Explanation
-----------------------------------

The main problem that NlpTools aims to solve is the problem of
classification. Either text categorization or Named Entity Recognition
and generally any type of classification problem.

We 'll analyze a bit some terms used in NlpTools.

1. Documents
2. Features & Feature factories

### Documents (under the folder documents)

The item that gets classified is called a document. A document can be
anything from a single word to a word with context or not even a word.

The `Document` interface only has one method `getDocumentData()`. This
method is used for the features (see below) and it can return anything.

In NlpTools there already exist three Document classes that will cover
most occasions.

1. `TokensDocument` is a class that represents a bag of words
   type of document. Its `getDocumentData()` simply returns an array
   of tokens.
2. `WordDocument` is a class that represents a single word but
   within the context of a larger document. Useful for Named Entity
   Recognition. Its `getDocumentData()` returns an array with the
   following structure `array(word,prev_context,next_context)`.
3. `TrainingDocument` is a class that "decorates" any other Document
   class and is useful for training because it also holds the actual
   label of the data.

There also exists a class `TrainingSet`. It is used to manage the
training and testing documents.   
It provides:

1. Easy creation of training documents
2. Easy iteration over all documents and their actual classes

### Features & Feature factories

Features are functions that return 1 or 0 given a document and a class.
Because the total seen features are usually in the tens of thousands
(actually more) features are encoded as a sparse array that only
contains the ids of the feature functions that return 1.

The ids of the features are unique strings. The most standard feature is
the use of the document itsself, the word or the words.

NlpTools uses feature factories. Interface `FeatureFactory` contains one
method, `getFeatureArray($class, Document $d)` which should return the
sparse array containing all the ids of all the features that fire for
this combination of class and document.

There already exist two feature factories.

1. `DataAsFeatures` simply returns the document data. Very useful for
    naive Bayes models combined with the `TokensDocument` as Document.
2. `FunctionFeatures` is a container of functions, the result of each
    as an array with unique elements is the resulting feature array.
    `FunctionFeatures` aims at promoting the reuse of the code of common
    features and the combination of them in creating models.

Example
-------

As an example we will create a sentiment detection classifier based on
a well known [imdb review dataset assembled by Pang/Lee](http://www.cs.cornell.edu/people/pabo/movie-review-data/).
We will be using version 2.

The example code can be found in tests folder.

```php
// Include the autoloader to use any part of the NlpTools
include("../autoloader.php");
```

Next we will instantiate classes that will be needed throughout both
training and testing. We create a simple whitespace tokenizer (because
the dataset is already tokenized). As features we use only the tokens
(words) themselves. Since we will use Maxent we prepend each word with
the class so that it only fires for a specific class.

```php
// create needed reusable objects
$tok = new NlpTools\WhitespaceTokenizer();
$ff = new NlpTools\FunctionFeatures();
$ff->add(function ($class, NlpTools\Document $d) {
	$r = array();
	foreach ($d->getDocumentData() as $tok)
		$r[] = $class.$tok;
	return $r;
});
```

```php
// create an empty training set and an empty model
$tset = new NlpTools\TrainingSet();
$model = new NlpTools\Maxent(array());

// a link to the optimizer will be added in the future
// for now one could put instead
// $optimizer = new NlpTools\MaxentGradientDescent();
// but it would take too long for such a large dataset
$optimizer = new NlpTools\ExternalMaxentOptimizer("path to optimizer/gradient-descent");
```

Read in the training files list and the test files list.

```php
$train = new SplFileObject($argv[1]);
$test = new SplFileObject($argv[2]);

foreach ($train as $f)
{
	$f = substr($f,0,-1); // get rid of the newline
	if (strlen($f)==0)
		continue;
	// the class is determined by the filename
	// ex.: .../data/neg/cv000_... is negative
	//      .../data/pos/cv000_... is positive
	$class = "neg";
	if (strpos($f,"pos")!==false)
	{
		$class = "pos";
	}
	// create a document and add it to the training set
}
```

Create a document and add it to the training set.

```php
$tset->addDocument(
			$class,
			new NlpTools\TokensDocument($tok->tokenize(file_get_contents($f)))
		);
```

Train the model. $ff is the feature factory, $tset the training set.

```php
$model->train($ff,$tset,$optimizer);
```

After we have trained our model, in order to use it for classification
we have to use it with a compatible classifier. Wherever possible
parameter types are explicitly declared in order to ensure that only
compatible types will be used (Which means that if not sure one can
check what type of model does Maxent implement and then use any
classifier that accepts that model as a parameter).

Maxent creates a set of weights for each feature, the linear
combination of the features with those weights decides the class of
the document so we will use the FeatureBasedLinearClassifier. We also
pass the feature factory to the classifier so that it can recreate
which features would fire for a given class.

Let's define some stuff:

* _ff(d,c)_ is the feature factory function and returns a vector with
   either 0 or 1 for each feature
* _w_ is our model which is a vector of real numbers and the same size
   as the one returned by the _ff(d,c)_ . It is calculated during
   training.

So the classification comes down to _argmax<sub>c</sub>(ff(d,c) • w)_
where • denotes the inner product of two vectors.

```php
$cls = new NlpTools\FeatureBasedLinearClassifier($ff,$model);
```

Finally we evaluate our model. For each test document we make a
prediction of its class and measure the accuracy of the predictions.

```php
$correct = 0;
$total = 0;
foreach ($test as $f)
{
	$f = substr($f,0,-1);
	if (strlen($f)==0)
		continue;
	$class = "neg";
	if (strpos($f,"pos")!==false)
	{
		$class = "pos";
	}
	$doc = new NlpTools\TokensDocument($tok->tokenize(file_get_contents($f)));
	$predicted = $cls->classify(array("pos","neg"),$doc);
	if ($predicted == $class)
	{
		$correct++;
	}
	$total++;
}

printf("Acc: %.2f%%\n",(100*$correct/$total));
```

With a random ten-fold [crossvalidation](http://en.wikipedia.org/wiki/Cross-validation_(statistics))
and training with the external optimizer the model achieves approximately
86% accuracy.

Note that since this is an averagely big dataset we need to run the script with
more loose memory constraints like
`php -d memory_limit=300M sentiment.php path/to/training path/to/test`
