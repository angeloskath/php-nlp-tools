<?php

declare(strict_types=1);

namespace NlpTools\Models;

use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\TrainingSet;

/**
 * Implement a MultinomialNBModel by training on a TrainingSet with a
 * FeatureFactoryInterface and additive smoothing.
 */
class FeatureBasedNB implements MultinomialNBModelInterface
{
    /**
     * Computed prior probabilities
     *
     * @var array<string, float>
     */
    protected array $priors = [];

    /**
     * Computed conditional probabilites
     *
     * @var array<string, mixed>
     */
    protected array $condprob = [];

    /**
     * Probability for each unknown word in a class a/(len(terms[class])+a*len(V))
     *
     * @var array<string, mixed>
     */
    protected array $unknown = [];

    /**
     * Return the prior probability of class $class
     * P(c) as computed by the training data
     */
    public function getPrior(string $class): float
    {
        return $this->priors[$class] ?? 0;
    }

    /**
     * Return the conditional probability of a term for a given class.
     *
     * @param  string $term  The term (word, feature id, ...)
     * @param  string $class The class
     */
    public function getCondProb(string $term, string $class): float
    {
        if (!isset($this->condprob[$term][$class])) {
            return $this->unknown[$class] ?? 0;
        }

        return $this->condprob[$term][$class];
    }

    /**
     * Train on the given set and fill the model's variables. Use the
     * training context provided to update the counts as if the training
     * set was appended to the previous one that provided the context.
     *
     * It can be used for incremental training. It is not meant to be used
     * with the same training set twice.
     *
     * @param array<string, mixed> $trainContext The previous training context
     * @param FeatureFactoryInterface $featureFactory A feature factory to compute features from a training document
     * @param TrainingSet $trainingSet The training set
     * @param integer $additiveSmoothing The parameter for additive smoothing. Defaults to add-one smoothing.
     * @return array<string, mixed>   Return a training context to be used for further incremental training,
     *               although this is not necessary since the changes also happen in place
     */
    public function trainWithContext(array &$trainContext, FeatureFactoryInterface $featureFactory, TrainingSet $trainingSet, int $additiveSmoothing = 1): array
    {
        $this->countTrainingSet(
            $featureFactory,
            $trainingSet,
            $trainContext['termcount_per_class'],
            $trainContext['termcount'],
            $trainContext['ndocs_per_class'],
            $trainContext['voc'],
            $trainContext['ndocs']
        );

        $voccount = count($trainContext['voc']);

        $this->computeProbabilitiesFromCounts(
            $trainingSet->getClassSet(),
            $trainContext['termcount_per_class'],
            $trainContext['termcount'],
            $trainContext['ndocs_per_class'],
            $trainContext['ndocs'],
            $voccount,
            $additiveSmoothing
        );

        return $trainContext;
    }

    /**
     * Train on the given set and fill the models variables
     *
     * priors[c] = NDocs[c]/NDocs
     * condprob[t][c] = count( t in c) + 1 / sum( count( t' in c ) + 1 , for every t' )
     * unknown[c] = condbrob['word that doesnt exist in c'][c] ( so that count(t in c)==0 )
     *
     * More information on the algorithm can be found at
     * http://nlp.stanford.edu/IR-book/html/htmledition/naive-bayes-text-classification-1.html
     *
     * @param FeatureFactoryInterface $featureFactory A feature factory to compute features from a training document
     * @param TrainingSet $trainingSet The training set
     * @param  integer $additiveSmoothing The parameter for additive smoothing. Defaults to add-one smoothing.
     * @return array<string, mixed>   Return a training context to be used for incremental training
     */
    public function train(FeatureFactoryInterface $featureFactory, TrainingSet $trainingSet, int $additiveSmoothing = 1): array
    {
        $class_set = $trainingSet->getClassSet();

        $ctx = ['termcount_per_class' => array_fill_keys($class_set, 0), 'termcount' => array_fill_keys($class_set, []), 'ndocs_per_class' => array_fill_keys($class_set, 0), 'voc' => [], 'ndocs' => 0];

        return $this->trainWithContext($ctx, $featureFactory, $trainingSet, $additiveSmoothing);
    }

    /**
     * Count all the features for each document. All parameters are passed
     * by reference and they are filled in this function. Useful for not
     * making copies of big arrays.
     *
     * @param FeatureFactoryInterface $featureFactory A feature factory to create the features for each document in the set
     * @param TrainingSet $trainingSet The training set (collection of labeled documents)
     * @param  array<string, int>      $termcountPerClass The count of occurences of each feature in each class
     * @param  array<string, int>      $termcount           The total count of occurences of each term
     * @param  array<string, int>      $ndocsPerClass     The total number of documents per class
     * @param  array<string, int>      $voc                 A set of the found features
     * @param  integer                 $ndocs               The number of documents
     * @return void
     */
    protected function countTrainingSet(FeatureFactoryInterface $featureFactory, TrainingSet $trainingSet, array &$termcountPerClass, array &$termcount, array &$ndocsPerClass, array &$voc, int &$ndocs)
    {
        foreach ($trainingSet as $tdoc) {
            $ndocs++;
            $c = $tdoc->getClass();
            $ndocsPerClass[$c]++;
            $features = $featureFactory->getFeatureArray($c, $tdoc);
            if (is_int(key($features))) {
                $features = array_count_values($features);
            }

            foreach ($features as $f => $fcnt) {
                if (!isset($voc[$f])) {
                    $voc[$f] = 0;
                }

                $termcountPerClass[$c] += $fcnt;
                if (isset($termcount[$c][$f])) {
                    $termcount[$c][$f] += $fcnt;
                } else {
                    $termcount[$c][$f] = $fcnt;
                }
            }
        }
    }

    /**
     * Compute the probabilities given the counts of the features in the
     * training set.
     *
     * @param  array<int, string>   $class_set           Just the array that contains the classes
     * @param  array<string, int>   $termcountPerClass The count of occurences of each feature in each class
     * @param  array<string, mixed>   $termcount           The total count of occurences of each term
     * @param  array<string, int>   $ndocsPerClass     The total number of documents per class
     * @param  integer $ndocs               The total number of documents
     * @param  integer $voccount            The total number of features found
     * @return void
     */
    protected function computeProbabilitiesFromCounts(array $class_set, array &$termcountPerClass, array &$termcount, array &$ndocsPerClass, int $ndocs, int $voccount, int $additiveSmoothing = 1)
    {
        $denom_smoothing = $additiveSmoothing * $voccount;
        foreach ($class_set as $class) {
            $this->priors[$class] = $ndocsPerClass[$class] / $ndocs;
            foreach ($termcount[$class] as $term => $count) {
                $this->condprob[$term][$class] = ($count + $additiveSmoothing) / ($termcountPerClass[$class] + $denom_smoothing);
            }
        }

        foreach ($class_set as $class) {
            $this->unknown[$class] = $additiveSmoothing / ($termcountPerClass[$class] + $denom_smoothing);
        }
    }

    /**
     * Just save the probabilities for reuse
     */
    public function __sleep()
    {
        return ['priors', 'condprob', 'unknown'];
    }
}
