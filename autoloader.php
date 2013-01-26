<?php
/* * * * * * * * * * * * * * * * *
 * Automatically generated file  *
 * ! Edit with caution           *
 * * * * * * * * * * * * * * * * */
 
 spl_autoload_register( function ($name) {
	 static $files = array(
		"NlpTools\FeatureBasedNB"=>"/models/feature_based_nb.php",
		"NlpTools\LinearModel"=>"/models/linear_model.php",
		"NlpTools\MultinomialNBModel"=>"/models/multinomial_nb.php",
		"NlpTools\Maxent"=>"/models/maxent.php",
		"NlpTools\DataAsFeatures"=>"/feature_factories/data_as_features.php",
		"NlpTools\FeatureFactory"=>"/feature_factories/feature_factory.php",
		"NlpTools\FunctionFeatures"=>"/feature_factories/callables_as_features.php",
		"NlpTools\MultinomialNBClassifier"=>"/classifier/multinomial_nb_classifier.php",
		"NlpTools\Classifier"=>"/classifier/classifier.php",
		"NlpTools\FeatureBasedLinearClassifier"=>"/classifier/feature_based_linear_classifier.php",
		"NlpTools\JaccardIndex"=>"/similarity/jaccard_index.php",
		"NlpTools\Simhash"=>"/similarity/simhash.php",
		"NlpTools\CosineSimilarity"=>"/similarity/cosine_similarity.php",
		"NlpTools\SetSimilarity"=>"/similarity/set_similarity.php",
		"NlpTools\SetDistance"=>"/similarity/set_distance.php",
		"NlpTools\RegexStemmer"=>"/stemmers/regex_stemmer.php",
		"NlpTools\Stemmer"=>"/stemmers/stemmer.php",
		"NlpTools\PorterStemmer"=>"/stemmers/porter_stemmer.php",
		"NlpTools\FeatureBasedLinearOptimizer"=>"/optimizers/feature_based_optimizer.php",
		"NlpTools\MaxentGradientDescent"=>"/optimizers/maxent_grad_descent.php",
		"NlpTools\ExternalMaxentOptimizer"=>"/optimizers/external_maxent_optimizer.php",
		"NlpTools\MaxentOptimizer"=>"/optimizers/maxent.php",
		"NlpTools\GradientDescentOptimizer"=>"/optimizers/grad_descent.php",
		"NlpTools\Tokenizer"=>"/tokenizer/tokenizer.php",
		"NlpTools\WhitespaceTokenizer"=>"/tokenizer/whitespace_tokenizer.php",
		"NlpTools\ClassifierBasedTokenizer"=>"/tokenizer/classifier_based_tokenizer.php",
		"NlpTools\WhitespaceAndPunctuationTokenizer"=>"/tokenizer/whitespace_punctuation_tokenizer.php",
		"NlpTools\TokensDocument"=>"/documents/tokens_document.php",
		"NlpTools\Document"=>"/documents/document.php",
		"NlpTools\TrainingSet"=>"/documents/training_set.php",
		"NlpTools\WordDocument"=>"/documents/word_document.php",
		"NlpTools\TrainingDocument"=>"/documents/training_document.php",
	);
	
	if (isset($files[$name]))
	{
		include(__DIR__.$files[$name]);
	}
	else
	{
		throw new Exception("Class not found $name");
	}
 });

?>
