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
		"NlpTools\Models\Lda"=>"/models/lda.php",
		"NlpTools\Models\Maxent"=>"/models/maxent.php",
		"NlpTools\Tokenizers\Tokenizer"=>"/tokenizers/tokenizer.php",
		"NlpTools\Tokenizers\WhitespaceTokenizer"=>"/tokenizers/whitespace_tokenizer.php",
		"NlpTools\Tokenizers\ClassifierBasedTokenizer"=>"/tokenizers/classifier_based_tokenizer.php",
		"NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer"=>"/tokenizers/whitespace_punctuation_tokenizer.php",
		"NlpTools\FeatureFactories\DataAsFeatures"=>"/feature_factories/data_as_features.php",
		"NlpTools\FeatureFactories\FeatureFactory"=>"/feature_factories/feature_factory.php",
		"NlpTools\FeatureFactories\FunctionFeatures"=>"/feature_factories/callables_as_features.php",
		"NlpTools\Random\Generators\GeneratorI"=>"/random/src/NlpTools/Random/Generators/GeneratorI.php",
		"NlpTools\Random\Generators\FromFile"=>"/random/src/NlpTools/Random/Generators/FromFile.php",
		"NlpTools\Random\Generators\MersenneTwister"=>"/random/src/NlpTools/Random/Generators/MersenneTwister.php",
		"NlpTools\Random\Distributions\Normal"=>"/random/src/NlpTools/Random/Distributions/Normal.php",
		"NlpTools\Random\Distributions\Gamma"=>"/random/src/NlpTools/Random/Distributions/Gamma.php",
		"NlpTools\Random\Distributions\Dirichlet"=>"/random/src/NlpTools/Random/Distributions/Dirichlet.php",
		"NlpTools\Random\Distributions\AbstractDistribution"=>"/random/src/NlpTools/Random/Distributions/AbstractDistribution.php",
		"NlpTools\Clustering\CentroidFactories\Hamming"=>"/clustering/hamming_centroid.php",
		"NlpTools\Clustering\CentroidFactories\Euclidean"=>"/clustering/euclidean_centroid.php",
		"NlpTools\Clustering\CentroidFactories\CentroidFactory"=>"/clustering/centroid_factory.php",
		"NlpTools\Clustering\Clusterer"=>"/clustering/cluster.php",
		"NlpTools\Clustering\KMeans"=>"/clustering/k_means.php",
		"NlpTools\Clustering\CentroidFactories\MeanAngle"=>"/clustering/mean_angle_centroid.php",
		"NlpTools\Classifiers\MultinomialNBClassifier"=>"/classifier/multinomial_nb_classifier.php",
		"NlpTools\Classifiers\Classifier"=>"/classifier/classifier.php",
		"NlpTools\Classifiers\FeatureBasedLinearClassifier"=>"/classifier/feature_based_linear_classifier.php",
		"NlpTools\Similarity\JaccardIndex"=>"/similarity/jaccard_index.php",
		"NlpTools\Similarity\Euclidean"=>"/similarity/euclidean.php",
		"NlpTools\Similarity\Distance"=>"/similarity/distance.php",
		"NlpTools\Similarity\Simhash"=>"/similarity/simhash.php",
		"NlpTools\Similarity\CosineSimilarity"=>"/similarity/cosine_similarity.php",
		"NlpTools\Similarity\Similarity"=>"/similarity/similarity.php",
		"NlpTools\Similarity\HammingDistance"=>"/similarity/hamming.php",
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
