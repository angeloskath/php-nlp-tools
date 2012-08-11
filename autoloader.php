<?php
/* * * * * * * * * * * * * * * * *
 * Automatically generated file  *
 * ! Edit with caution           *
 * * * * * * * * * * * * * * * * */
 
 spl_autoload_register( function ($name) {
	 static $files = array(
		"NlpTools\LinearModel"=>"/models/linear_model.php",
		"NlpTools\Maxent"=>"/models/maxent.php",
		"NlpTools\FeatureFactory"=>"/feature_factories/feature_factory.php",
		"NlpTools\FunctionFeatures"=>"/feature_factories/callables_as_features.php",
		"NlpTools\Classifier"=>"/classifier/classifier.php",
		"NlpTools\FeatureBasedLinearClassifier"=>"/classifier/feature_based_linear_classifier.php",
		"NlpTools\FeatureBasedLinearOptimizer"=>"/optimizers/feature_based_optimizer.php",
		"NlpTools\MaxentGradientDescent"=>"/optimizers/maxent_grad_descent.php",
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
