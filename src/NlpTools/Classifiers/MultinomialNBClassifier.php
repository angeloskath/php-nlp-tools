<?php

namespace NlpTools\Classifiers;

use NlpTools\Documents\DocumentInterface;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Models\MultinomialNBModelInterface;

/**
 * Use a multinomia NB model to classify a document
 */
class MultinomialNBClassifier implements ClassifierInterface
{
    // The feature factory
    protected $feature_factory;
    // The NBModel
    protected $model;

    public function __construct(FeatureFactoryInterface $ff, MultinomialNBModelInterface $m)
    {
        $this->feature_factory = $ff;
        $this->model = $m;
    }

    /**
     * Compute the probability of $d belonging to each class
     * successively and return that class that has the maximum
     * probability.
     *
     * @param  array             $classes The classes from which to choose
     * @param  DocumentInterface $d       The document to classify
     * @return string            $class The class that has the maximum probability
     */
    public function classify(array $classes, DocumentInterface $d)
    {
        $maxclass = current($classes);
        $maxscore = $this->getScore($maxclass,$d);
        while ($class=next($classes)) {
            $score = $this->getScore($class,$d);
            if ($score>$maxscore) {
                $maxclass = $class;
                $maxscore = $score;
            }
        }

        return $maxclass;
    }

    /**
     * Compute the log of the probability of the Document $d belonging
     * to class $class. We compute the log so that we can sum over the
     * logarithms instead of multiplying each probability.
     *
     * @todo perhaps MultinomialNBModel should have precomputed the logs
     *       ex.: getLogPrior() and getLogCondProb()
     *
     * @param string $class The class for which we are getting a score
     * @param DocumentInterface The document whose score we are getting
     * @return float The log of the probability of $d belonging to $class
     */
    public function getScore($class, DocumentInterface $d)
    {
        $score = log($this->model->getPrior($class));
        $features = $this->feature_factory->getFeatureArray($class,$d);
        if (is_int(key($features)))
            $features = array_count_values($features);
        foreach ($features as $f=>$fcnt) {
            $score += $fcnt*log($this->model->getCondProb($f,$class));
        }

        return $score;
    }

}
