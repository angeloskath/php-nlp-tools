<?php
/* * * * * * * * * * * * * * * * *
 * Automatically generated file  *
 * ! Edit with caution           *
 * * * * * * * * * * * * * * * * */
 
 spl_autoload_register( function ($name) {
	 static $files = array(
		"NlpTools\Models\FeatureBasedNB"=>"/models/feature_based_nb.php",
		"NlpTools\Models\LinearModel"=>"/models/linear_model.php",
		"NlpTools\Models\MultinomialNBModel"=>"/models/multinomial_nb.php",
		"NlpTools\Models\Maxent"=>"/models/maxent.php",
		"NlpTools\Tokenizers\Tokenizer"=>"/tokenizers/tokenizer.php",
		"NlpTools\Tokenizers\WhitespaceTokenizer"=>"/tokenizers/whitespace_tokenizer.php",
		"NlpTools\Tokenizers\ClassifierBasedTokenizer"=>"/tokenizers/classifier_based_tokenizer.php",
		"NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer"=>"/tokenizers/whitespace_punctuation_tokenizer.php",
		"NlpTools\FeatureFactories\DataAsFeatures"=>"/feature_factories/data_as_features.php",
		"NlpTools\FeatureFactories\FeatureFactory"=>"/feature_factories/feature_factory.php",
		"NlpTools\FeatureFactories\FunctionFeatures"=>"/feature_factories/callables_as_features.php",
		"NlpTools\Classifiers\MultinomialNBClassifier"=>"/classifier/multinomial_nb_classifier.php",
		"NlpTools\Classifiers\Classifier"=>"/classifier/classifier.php",
		"NlpTools\Classifiers\FeatureBasedLinearClassifier"=>"/classifier/feature_based_linear_classifier.php",
		"NlpTools\Similarity\JaccardIndex"=>"/similarity/jaccard_index.php",
		"NlpTools\Similarity\Simhash"=>"/similarity/simhash.php",
		"NlpTools\Similarity\CosineSimilarity"=>"/similarity/cosine_similarity.php",
		"NlpTools\Similarity\SetSimilarity"=>"/similarity/set_similarity.php",
		"NlpTools\Similarity\SetDistance"=>"/similarity/set_distance.php",
		"NlpTools\Stemmers\RegexStemmer"=>"/stemmers/regex_stemmer.php",
		"NlpTools\Stemmers\Stemmer"=>"/stemmers/stemmer.php",
		"NlpTools\Stemmers\PorterStemmer"=>"/stemmers/porter_stemmer.php",
		"NlpTools\Optimizers\FeatureBasedLinearOptimizer"=>"/optimizers/feature_based_optimizer.php",
		"NlpTools\Optimizers\MaxentGradientDescent"=>"/optimizers/maxent_grad_descent.php",
		"NlpTools\Optimizers\ExternalMaxentOptimizer"=>"/optimizers/external_maxent_optimizer.php",
		"NlpTools\Optimizers\MaxentOptimizer"=>"/optimizers/maxent.php",
		"NlpTools\Optimizers\GradientDescentOptimizer"=>"/optimizers/grad_descent.php",
		"NlpTools\Documents\TokensDocument"=>"/documents/tokens_document.php",
		"NlpTools\Documents\Document"=>"/documents/document.php",
		"NlpTools\Documents\TrainingSet"=>"/documents/training_set.php",
		"NlpTools\Documents\WordDocument"=>"/documents/word_document.php",
		"NlpTools\Documents\TrainingDocument"=>"/documents/training_document.php",
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