<?php

namespace NlpTools\Models;

use NlpTools\FeatureFactories\FeatureFactoryInterface;
use NlpTools\Documents\TrainingSet;
use NlpTools\Random\Generators\MersenneTwister;

/**
 * Topic discovery with latent dirchlet allocation using gibbs sampling.
 *
 * The implementation is based on the paper by Griffiths and Steyvers
 * that can be found http://www.ncbi.nlm.nih.gov/pmc/articles/PMC387300/.
 *
 * It is also heavily influenced (especially on the implementation and
 * debugging of the online gibbs sampler) by the python implementation
 * by Mathieu Blondel at https://gist.github.com/mblondel/542786
 *
 * @author Angelos Katharopoulos <katharas@gmail.com>
 */
class Lda
{
    protected $ff;

    protected $ntopics;
    protected $a;
    protected $b;

    protected $mt;

    protected $count_docs_topics;
    protected $count_topics_words;
    protected $words_in_doc;
    protected $words_in_topic;
    protected $word_doc_assigned_topic;
    protected $voccnt;
    protected $voc;

    /**
     * @param FeatureFactoryInterface $ff      The feature factory will be applied to each document and the resulting feature array will be considered as a document for LDA
     * @param integer                 $ntopics The number of topics assumed by the model
     * @param float                   $a       The dirichlet prior assumed for the per document topic distribution
     * @param float                   $b       The dirichlet prior assumed for the per word topic distribution
     */
    public function __construct(FeatureFactoryInterface $ff,$ntopics,$a=1,$b=1)
    {
        $this->ff = $ff;

        $this->ntopics = $ntopics;
        $this->a = $a;
        $this->b = $b;

        $this->mt = new MersenneTwister();
    }

    /**
     * Generate an array suitable for use with Lda::initialize and
     * Lda::gibbsSample from a training set.
     */
    public function generateDocs(TrainingSet $tset)
    {
        $docs = array();
        foreach ($tset as $d)
            $docs[] = $this->ff->getFeatureArray('',$d);

        return $docs;
    }

    /**
     * Count initially the co-occurences of documents,topics and
     * topics,words and cache them to run Gibbs sampling faster
     *
     * @param array $docs The docs that we will use to generate the sample
     */
    public function initialize(array &$docs)
    {
        $doc_keys = range(0,count($docs)-1);
        $topic_keys = range(0,$this->ntopics-1);

        // initialize the arrays
        $this->words_in_doc = array_fill_keys(
            $doc_keys,
            0
        );
        $this->words_in_topic = array_fill_keys(
            $topic_keys,
            0
        );
        $this->count_docs_topics = array_fill_keys(
            $doc_keys,
            array_fill_keys(
                $topic_keys,
                0
            )
        );
        $this->count_topics_words = array_fill_keys(
            $topic_keys,
            array()
        );
        $this->word_doc_assigned_topic = array_fill_keys(
            $doc_keys,
            array()
        );
        $this->voc = array();

        foreach ($docs as $i=>$doc) {
            $this->words_in_doc[$i] = count($doc);
            foreach ($doc as $idx=>$w) {
                // choose a topic randomly to assign this word to
                $topic = (int) ($this->mt->generate()*$this->ntopics);

                //$this->words_in_doc[$i]++;
                $this->words_in_topic[$topic]++;
                $this->count_docs_topics[$i][$topic]++;

                if (!isset($this->count_topics_words[$topic][$w]))
                    $this->count_topics_words[$topic][$w]=0;
                $this->count_topics_words[$topic][$w]++;

                $this->word_doc_assigned_topic[$i][$idx] = $topic;

                $this->voc[$w] = 1;
            }
        }
        $this->voccnt = count($this->voc);
        $this->voc = array_keys($this->voc);
    }

    /**
     * Run the gibbs sampler $it times.
     *
     * @param TrainingSet The docs to run lda on
     * @param $it The number of iterations to run
     */
    public function train(TrainingSet $tset,$it)
    {
        $docs = $this->generateDocs($tset);

        $this->initialize($docs);

        while ($it-- > 0) {
            $this->gibbsSample($docs);
        }
     }

     /**
      * Generate one gibbs sample.
      * The docs must have been passed to initialize previous to calling
      * this function.
      *
      * @param array $docs The docs that we will use to generate the sample
      */
    public function gibbsSample(array &$docs)
    {
        foreach ($docs as $i=>$doc) {
            foreach ($doc as $idx=>$w) {
                // remove word $w from the dataset
                $topic = $this->word_doc_assigned_topic[$i][$idx];
                $this->count_docs_topics[$i][$topic]--;
                $this->count_topics_words[$topic][$w]--;
                $this->words_in_topic[$topic]--;
                $this->words_in_doc[$i]--;
                // ---------------------------

                // recompute the probabilities of all topics and
                // resample a topic for this word $w
                $p_topics = $this->conditionalDistribution($i,$w);
                $topic = $this->drawIndex($p_topics);
                // ---------------------------

                // add word $w back into the dataset
                if (!isset($this->count_topics_words[$topic][$w]))
                    $this->count_topics_words[$topic][$w]=0;
                $this->count_topics_words[$topic][$w]++;

                $this->count_docs_topics[$i][$topic]++;
                $this->words_in_topic[$topic]++;
                $this->words_in_doc[$i]++;
                $this->word_doc_assigned_topic[$i][$idx] = $topic;
                // ---------------------------
            }
        }
     }

     /**
      * Get the probability of a word given a topic (phi according to
      * Griffiths and Steyvers)
      *
      * @param $limit_words Limit the results to the top n words
      * @return array A two dimensional array that contains the probabilities for each topic
      */
    public function getWordsPerTopicsProbabilities($limit_words=-1)
    {
         $p_t_w = array_fill_keys(
            range(0,$this->ntopics-1),
            array()
         );
         foreach ($p_t_w as $topic=>&$p) {
             $denom = $this->words_in_topic[$topic]+$this->voccnt*$this->b;
             foreach ($this->voc as $w) {
                 if (isset($this->count_topics_words[$topic][$w]))
                    $p[$w] = $this->count_topics_words[$topic][$w]+$this->b;
                 else
                    $p[$w] = $this->b;
                 $p[$w] /= $denom;
             }
             if ($limit_words>0) {
                 arsort($p);
                 $p = array_slice($p,0,$limit_words,true); // true to preserve the keys
             }
         }

         return $p_t_w;
     }

     /**
      * Shortcut to getWordsPerTopicsProbabilities
      */
     public function getPhi($limit_words=-1)
     {
         return $this->getWordsPerTopicsProbabilities($limit_words);
     }

     /**
      * Get the probability of a document given a topic (theta according
      * to Griffiths and Steyvers)
      *
      * @param $limit_docs Limit the results to the top n docs
      * @return array A two dimensional array that contains the probabilities for each document
      */
     public function getDocumentsPerTopicsProbabilities($limit_docs=-1)
     {
         $p_t_d = array_fill_keys(
            range(0,$this->ntopics-1),
            array()
         );

         $doccnt = count($this->words_in_doc);
         $denom = $doccnt + $this->ntopics*$this->a;
         $count_topics_docs = array();
         foreach ($this->count_docs_topics as $doc=>$topics) {
             foreach ($topics as $t=>$c)
                $count_topics_docs[$doc][$t]++;
         }

         foreach ($p_t_d as $topic=>&$p) {
             foreach ($count_topics_docs as $doc=>$tc) {
                 $p[$doc] = ($tc[$topic] + $this->a)/$denom;
             }
             if ($limit_words>0) {
                 arsort($p);
                 $p = array_slice($p,0,$limit_words,true); // true to preserve the keys
             }
         }

         return $p;
     }

     /**
      * Shortcut to getDocumentsPerTopicsProbabilities
      */
     public function getTheta($limit_docs=-1)
     {
         return $this->getDocumentsPerTopicsProbabilities($limit_docs);
     }

     /**
      * Log likelihood of the model having generated the data as
      * implemented by M. Blondel
      */
     public function getLogLikelihood()
     {
         $voccnt = $this->voccnt;
         $lik = 0;
         $b = $this->b;
         $a = $this->a;
         foreach ($this->count_topics_words as $topic=>$words) {
             $lik += $this->log_multi_beta(
                $words,
                $b
             );
             $lik -= $this->log_multi_beta(
                $b,
                0,
                $voccnt
             );
         }
         foreach ($this->count_docs_topics as $doc=>$topics) {
             $lik += $this->log_multi_beta(
                $topics,
                $a
             );
             $lik -= $this->log_multi_beta(
                $a,
                0,
                $this->ntopics
             );
         }

         return $lik;
     }

     /**
      * This is the implementation of the equation number 5 in the paper
      * by Griffiths and Steyvers.
      *
      * @return array The vector of probabilites for all topics as computed by the equation 5
      */
     protected function conditionalDistribution($i,$w)
     {
         $p = array_fill_keys(range(0,$this->ntopics-1),0);
         for ($topic=0;$topic<$this->ntopics;$topic++) {
            if (isset($this->count_topics_words[$topic][$w]))
                $numerator = $this->count_topics_words[$topic][$w]+$this->b;
            else
                $numerator = $this->b;

            $numerator *= $this->count_docs_topics[$i][$topic]+$this->a;

            $denominator = $this->words_in_topic[$topic]+$this->voccnt*$this->b;
            $denominator *= $this->words_in_doc[$i]+$this->ntopics*$this->a;

            $p[$topic] = $numerator/$denominator;
         }

         // divide by sum to obtain probabilities
         $sum = array_sum($p);

         return array_map(
            function ($p) use ($sum) {
                return $p/$sum;
            },
            $p
         );
     }

     /**
      * Draw once from a multinomial distribution and return the index
      * of that is drawn.
      *
      * @return int The index that was drawn.
      */
     protected function drawIndex(array $d)
     {
         $x = $this->mt->generate();
         $p = 0.0;
         foreach ($d as $i=>$v) {
             $p+=$v;
             if ($p > $x)
                return $i;
         }
     }

     /**
      * Gamma function from picomath.org
      * see http://picomath.org/php/gamma.php.html for commented
      * implementation
      *
      * TODO: These should probably move outside of NlpTools together
      * with the Random namespace and form a nice php math library
      */
    private function gamma($x)
    {
        $gamma = 0.577215664901532860606512090; # Euler's gamma constant
        if ($x < 0.001) {
            return 1.0/($x*(1.0 + $gamma*$x));
        }
        if ($x < 12.0) {
            # The algorithm directly approximates gamma over (1,2) and uses
            # reduction identities to reduce other arguments to this interval.
            $y = $x;
            $n = 0;
            $arg_was_less_than_one = ($y < 1.0);
            # Add or subtract integers as necessary to bring y into (1,2)
            # Will correct for this below
            if ($arg_was_less_than_one) {
                $y += 1.0;
            } else {
                $n = floor($y) - 1;  # will use n later
                $y -= $n;
            }
            # numerator coefficients for approximation over the interval (1,2)
            $p =
            array(
                -1.71618513886549492533811E+0,
                 2.47656508055759199108314E+1,
                -3.79804256470945635097577E+2,
                 6.29331155312818442661052E+2,
                 8.66966202790413211295064E+2,
                -3.14512729688483675254357E+4,
                -3.61444134186911729807069E+4,
                 6.64561438202405440627855E+4
            );

            # denominator coefficients for approximation over the interval (1,2)
            $q =
            array(
                -3.08402300119738975254353E+1,
                 3.15350626979604161529144E+2,
                -1.01515636749021914166146E+3,
                -3.10777167157231109440444E+3,
                 2.25381184209801510330112E+4,
                 4.75584627752788110767815E+3,
                -1.34659959864969306392456E+5,
                -1.15132259675553483497211E+5
            );

            $num = 0.0;
            $den = 1.0;

            $z = $y - 1;
            for ($i = 0; $i < 8; $i++) {
                $num = ($num + $p[$i])*$z;
                $den = $den*$z + $q[$i];
            }
            $result = $num/$den + 1.0;

            # Apply correction if argument was not initially in (1,2)
            if ($arg_was_less_than_one) {
                # Use identity gamma(z) = gamma(z+1)/z
                # The variable "result" now holds gamma of the original y + 1
                # Thus we use y-1 to get back the orginal y.
                $result /= ($y-1.0);
            } else {
                # Use the identity gamma(z+n) = z*(z+1)* ... *(z+n-1)*gamma(z)
                for ($i = 0; $i < $n; $i++) {
                    $result *= $y++;
                }
            }

            return $result;
        }

        ###########################################################################
        # Third interval: [12, infinity)

        if ($x > 171.624) {
            # Correct answer too large to display.

            return Double.POSITIVE_INFINITY;
        }

        return exp($this->log_gamma($x));
    }
    private function log_gamma($x)
    {
        if ($x < 12.0) {
            return log(abs($this->gamma($x)));
        }

        # Abramowitz and Stegun 6.1.41
        # Asymptotic series should be good to at least 11 or 12 figures
        # For error analysis, see Whittiker and Watson
        # A Course in Modern Analysis (1927), page 252

        $c =
        array(
             1.0/12.0,
            -1.0/360.0,
             1.0/1260.0,
            -1.0/1680.0,
             1.0/1188.0,
            -691.0/360360.0,
             1.0/156.0,
            -3617.0/122400.0
        );
        $z = 1.0/($x*$x);
        $sum = $c[7];
        for ($i=6; $i >= 0; $i--) {
            $sum *= $z;
            $sum += $c[$i];
        }
        $series = $sum/$x;

        $halfLogTwoPi = 0.91893853320467274178032973640562;
        $logGamma = ($x - 0.5)*log($x) - $x + $halfLogTwoPi + $series;

        return $logGamma;
    }

    private function log_gamma_array($a)
    {
        foreach ($a as &$x)
            $x = $this->log_gamma($x);

        return $a;
    }
    private function log_multi_beta($a,$y=0,$k=null)
    {
        if ($k==null) {
            $ay = array_map(
                function ($x) use ($y) {
                    return $x+$y;
                },
                $a
            );

            return array_sum(
                $this->log_gamma_array(
                    $ay
                )
            )-$this->log_gamma(
                array_sum(
                    $ay
                )
            );
        } else {
            return $k*$this->log_gamma($a) - $this->log_gamma($k*$a);
        }
    }
}
