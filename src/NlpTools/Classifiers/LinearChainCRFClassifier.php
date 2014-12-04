<?php

namespace NlpTools\Classifiers;

use NlpTools\Models\LinearModel;
use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\TrainingSet;

/**
 * Assume the model was trained with LinearChainDocumentSet and the class
 * chains are represented by class names separated with '|'. To compute the
 * sequence's best use beam search (limited memory best first search).
 */
class LinearChainCRFClassifier implements SequenceClassifierInterface
{
    protected $ff;
    protected $model;
    protected $chainLength;
    protected $keepBest;

    /**
     * @param FeatureFactoryInterface $ff          The feature factory
     * @param LinearModel             $model       The model
     * @param int                     $chainLength How many past tags are in the chain
     * @param int                     $keepBest    How many of the best candidates to keep
     */
    public function __construct(
        FeatureFactoryInterface $ff,
        LinearModel $model,
        $chainLength = 1,
        $keepBest = 10
    ) {
        $this->ff = $ff;
        $this->model = $model;
        $this->chainLength = $chainLength;
        $this->keepBest = $keepBest;
    }

    /**
     * Implement the beam search in order to find the best sequence of tags for
     * this sequence of documents. As a normalization in the score in order to
     * not favor the longer sequences use the simple division by the length of
     * tags.
     *
     * Notice that this is a variation of the beam search algorithm because we
     * don't just expand the best and the recalculate the score and place it in
     * the queue.
     *
     * TODO: Consider implementing beam search by the book
     *
     * {@inheritdoc}
     */
    public function classify(array $classes, TrainingSet $docs)
    {
        $sortbyscore = function ($a, $b) {
            //return  ($b[0]/count($b[1])) > ($a[0]/count($a[1])) ? 1 : -1;
            return  $b[0] > $a[0] ? 1 : -1;
        };

        // as a transition table we will use from evreywhere to everywhere, so
        // we will just loop over the $classes array each time

        // add the initial states
        $queue = array();
        foreach ($classes as $class) {
            $queue[] = array($this->model->getVote($class, $this->ff, $docs[0]), array($class));
        }
        usort($queue, $sortbyscore);
        $queue = array_slice($queue, 0, $this->keepBest);

        // run adding the next step each time
        $doccnt = count($docs);
        for ($i=1; $i<$doccnt; $i++) {
            // expand each path in the queue
            $new = array();
            while ($queue) {
                list($vote, $sequence) = array_pop($queue);
                foreach ($classes as $class) {
                    // calculate the path to going to any other class from this
                    // one
                    $new[] = array(
                        $vote+$this->model->getVote(
                            implode("|", array_slice($sequence, -$this->chainLength))."|".$class,
                            $this->ff,
                            $docs[$i]
                        ),
                        array_merge(
                            $sequence,
                            array($class)
                        )
                    );
                }
            }

            // now sort by score and keep just a handful of the best ones
            usort($new, $sortbyscore);
            $queue = array_slice($new, 0, $this->keepBest);
        }

        return $queue[0][1];
    }
}
