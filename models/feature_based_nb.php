<?php

namespace NlpTools;

/*
 * Implement a MultinomialNBModel by training on a TrainingSet with a
 * FeatureFactory and additive smoothing.
 */
class FeatureBasedNB implements MultinomialNBModel
{
	// computed prior probabilities
	protected $priors;
	// computed conditional probabilites
	protected $condprob;
	// probability for each unknown word in a class a/(len(terms[class])+a*len(V))
	protected $unknown;
	
	public function __construct() {
		$this->priors = array();
		$this->condprob = array();
		$this->unknown = array();
	}
	
	/*
	 * Return the prior probability of class $class
	 * P(c) as computed by the training data
	 * 
	 * name: getPrior
	 * @param $class
	 * @return float prior probability
	 */
	public function getPrior($class) {
		return $this->priors[$class];
	}
	/*
	 * Return the conditional probability of a term for a given class.
	 * 
	 * name: getCondProb
	 * @param $term The term (word, feature id, ...)
	 * @param $class The class
	 * @return float
	 */
	public function getCondProb($term,$class) {
		if (!isset($this->condprob[$term][$class]))
			return $this->unknown[$class];
		else
			return $this->condprob[$term][$class];
	}
	
	/*
	 * Train on the given set and fill the models variables
	 * 
	 * priors[c] = NDocs[c]/NDocs
	 * condprob[t][c] = count( t in c) + 1 / sum( count( t' in c ) + 1 , for every t' )
	 * unknown[c] = condbrob['word that doesnt exist in c'][c] ( so that count(t in c)==0 )
	 * 
	 * More information on the algorithm can be found at
	 * http://nlp.stanford.edu/IR-book/html/htmledition/naive-bayes-text-classification-1.html
	 * 
	 * name: train
	 * @param FeatureFactory A feature factory to compute features from a training document
	 * @param TrainingSet The training set
	 * @param $a_smoothing The parameter for additive smoothing. Defaults to add-one smoothing.
	 * @return void
	 */
	public function train(FeatureFactory $ff, TrainingSet $tset, $a_smoothing=1) {
		$classSet = $tset->getClassSet();
		$ndocs = 0;
		$ndocs_per_class = array_fill_keys($classSet,0);
		$tc_per_class = array_fill_keys($classSet,0);
		$tc = array_fill_keys($classSet,array());
		$voc = array();
		
		foreach ($tset as $tdoc)
		{
			$ndocs++;
			$c = $tdoc->getClass();
			$ndocs_per_class[$c]++;
			$features = $ff->getFeatureArray($c,$tdoc);
			foreach ($features as $f)
			{
				if (!isset($voc[$f]))
					$voc[$f] = 0;
				
				$tc_per_class[$c]++;
				if (isset($tc[$c][$f]))
					$tc[$c][$f]++;
				else
					$tc[$c][$f] = 1;
			}
		}
		
		$voccount = count($voc);
		$denom_smoothing = $a_smoothing*$voccount;
		foreach ($classSet as $class)
		{
			$this->priors[$class] = $ndocs_per_class[$class] / $ndocs;
			foreach ($tc[$class] as $term=>$count)
			{
				$this->condprob[$term][$class] = ($count + $a_smoothing) / ($tc_per_class[$class] + $denom_smoothing);
			}
		}
		foreach ($classSet as $class)
		{
			$this->unknown[$class] = $a_smoothing / ($tc_per_class[$class] + $denom_smoothing);
		}
	}
	
	public function __sleep() {
		return array('priors','condprob','unknown');
	}
}

?>
