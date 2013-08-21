<?php

namespace NlpTools\Stemmers;

/**
 * Copyright 2013 Katharopoulos Angelos <katharas@gmail.com>
 *
 * This class implements the Porter stemming algorithm. It is almost a
 * one to one conversion from Porter's ANSI C implementation and can
 * thus be regarded as canonical as the C implementation found at
 * http://www.tartarus.org/~martin/PorterStemmer
 *
 * The rewrite instead of using Richard Heyes's implementation has
 * been to improve performance. I tried to keep as close to the C
 * implementation, minimize string creations (change in place) and
 * avoid regexes.
 *
 * The result is a bit more than 25% faster algorithm with php 5.3 (not
 * that much but since I wrote I thought I'd keep it).
 *
 */
class PorterStemmer extends Stemmer
{
    // isset is faster than switch in php even for one character switches
    protected static $vowels = array('a'=>'a','e'=>'e','i'=>'i','o'=>'o','u'=>'u');

    /**
     * Quoting from the original C implementation.
     *
     *	> The main part of the stemming algorithm starts here. b is a buffer
     *	> holding the word to be stemmed. The letters are in b[k0], b[k0+1] ...
     *	> ending at b[k]. In fact k0 = 0 in this demo program. k is readjusted
     *	> downwards as the stemming progresses. Zero termination is not in fact
     *	> used in the algorithm.
     *	>
     *	> Note that only lower case sequences are stemmed. Forcing to lower case
     *	> should be done before stem(...) is called.
     *
     * $b is a string holding one lower case word. $k0 is always 0 in
     * our case so it is removed. $k is readjusted to point to the end
     * of the string and b is changed so at the end b[0:k] will hold
     * the stem.
     *
     */
    private $b;
    private $k,$j;

    /* cons(i) is TRUE <=> b[i] is a consonant. */
    protected function cons($i)
    {
        if ($i>$this->k) {
            return true;
        }
        $c = $this->b[$i];
        if (isset(self::$vowels[$c])) {
            return false;
        } elseif ($c==='y') {
            return ($i===0) ? true : !$this->cons($i-1);
        } else {
            return true;
        }
    }

    /*
     * m() measures the number of consonant sequences between 0 and j. if c is
     * a consonant sequence and v a vowel sequence, and <..> indicates arbitrary
     * presence,
     *
     *   <c><v>       gives 0
     *   <c>vc<v>     gives 1
     *   <c>vcvc<v>   gives 2
     *   <c>vcvcvc<v> gives 3
     *   ....
     * */
    protected function m()
    {
        $n = 0;
        $i = 0;
        while (true) {
            if ($i > $this->j)
                return $n;
            if (! $this->cons($i))
                break;
            $i++;
        }
        $i++;
        while (true) {
            while (true) {
                if ($i > $this->j)
                    return $n;
                if ($this->cons($i))
                    break;
                $i++;
            }
            $i++;
            $n++;
            while (true) {
                if ($i > $this->j)
                    return $n;
                if (! $this->cons($i))
                    break;
                $i++;
            }
            $i++;
        }
    }

    /* vowelinstem() is TRUE <=> 0,...j contains a vowel */
    protected function vowelinstem()
    {
        for ($i = 0; $i <= $this->j; $i++) {
            if (! $this->cons($i))
                return true;
        }

        return false;
    }

    /* doublec(j) is TRUE <=> j,(j-1) contain a double consonant. */
    protected function doublec($j)
    {
        if ($j < 1)
            return false;
        if ($this->b[$j] != $this->b[$j-1])
            return false;
        return $this->cons($j);
    }

    /*
     * cvc(i) is TRUE <=> i-2,i-1,i has the form consonant - vowel - consonant
     * and also if the second c is not w,x or y. this is used when trying to
     * restore an e at the end of a short word. e.g.
     *
     *   cav(e), lov(e), hop(e), crim(e), but
     *   snow, box, tray.
     *
     * */
    protected function cvc($i)
    {
        if ($i < 2 || !$this->cons($i) || $this->cons($i-1) || !$this->cons($i-2))
            return false;
        $ch = $this->b[$i];
        if ($ch === 'w' || $ch === 'x' || $ch === 'y')
            return false;

        return true;
    }

    /*
     * ends(s) is TRUE <=> 0...k ends with the string s.
     *
     * $length is passed as a parameter because it provides a speedup.
     * */
    protected function ends($s,$length)
    {
        if ($s[$length-1] != $this->b[$this->k])
            return false;
        if ($length > $this->k+1)
            return false;
        if (substr_compare($this->b,$s,$this->k-$length+1,$length)!=0)
            return false;

        $this->j = $this->k-$length;

        return true;
    }

    /*
     * setto(s) sets (j+1),...k to the characters in the string s,
     * readjusting k.
     *
     * Again $length is passed for speedup
     * */
    protected function setto($s,$length)
    {
        $this->b = substr_replace($this->b,$s,$this->j+1);
        $this->k = $this->j+$length;
    }

    protected function r($s,$length)
    {
        if ($this->m()>0)
            $this->setto($s,$length);
    }

    /*
     * step1ab() gets rid of plurals and -ed or -ing. e.g.
     *
     *    caresses  ->  caress
     *    ponies    ->  poni
     *    ties      ->  ti
     *    caress    ->  caress
     *    cats      ->  cat
     *
     *    feed      ->  feed
     *    agreed    ->  agree
     *    disabled  ->  disable
     *
     *    matting   ->  mat
     *    mating    ->  mate
     *    meeting   ->  meet
     *    milling   ->  mill
     *    messing   ->  mess
     *
     *    meetings  ->  meet
     *
     * */
    protected function step1ab()
    {
        if ($this->b[$this->k] === 's') {
            if ($this->ends("sses",4))
                $this->k -= 2;
            else if ($this->ends("ies",3))
                $this->setto("i",1);
            else if ($this->b[$this->k-1] !== 's')
                $this->k--;
        }
        if ($this->ends("eed",3)) {
            if ($this->m() > 0)
                $this->k--;
        } elseif (($this->ends("ed",2) || $this->ends("ing",3)) && $this->vowelinstem()) {
            $this->k = $this->j;
            if ($this->ends("at",2))
                $this->setto("ate",3);
            else if ($this->ends("bl",2))
                $this->setto("ble",3);
            else if ($this->ends("iz",2))
                $this->setto("ize",3);
            else if ($this->doublec($this->k)) {
                $this->k--;
                $ch = $this->b[$this->k];
                if ($ch === 'l' || $ch === 's' || $ch === 'z')
                    $this->k++;
            } elseif ($this->m() === 1 && $this->cvc($this->k)) {
                $this->setto("e",1);
            }
        }
    }

    /*
     * step1c() turns terminal y to i when there is another
     * vowel in the stem.
     *
     * */
    protected function step1c()
    {
        if ($this->ends("y",1) && $this->vowelinstem())
            $this->b[$this->k] = 'i';
    }

    /*
     * step2() maps double suffices to single ones. so -ization
     * ( = -ize plus -ation) maps to -ize etc. note that the string
     * before the suffix must give m() > 0.
     *
     * */
    protected function step2()
    {
        switch ($this->b[$this->k-1]) {
            case 'a':
                if ($this->ends("ational",7)) { $this->r("ate",3); break; }
                if ($this->ends("tional",6)) { $this->r("tion",4); break; }
                break;
            case 'c':
                if ($this->ends("enci",4)) { $this->r("ence",4); break; }
                if ($this->ends("anci",4)) { $this->r("ance",4); break; }
                break;
            case 'e':
                if ($this->ends("izer",4)) { $this->r("ize",3); break; }
                break;
            case 'l':
                if ($this->ends("bli",3)) { $this->r("ble",3); break; }
                // -DEPARTURE-
                // To match the published algorithm, replace the above line with
                // if ($this->ends("abli",4)) { $this->r("able",4); break; }
                if ($this->ends("alli",4)) { $this->r("al",2); break; }
                if ($this->ends("entli",5)) { $this->r("ent",3); break; }
                if ($this->ends("eli",3)) { $this->r("e",1); break; }
                if ($this->ends("ousli",5)) { $this->r("ous",3); break; }
                break;
            case 'o':
                if ($this->ends("ization",7)) { $this->r("ize",3); break; }
                if ($this->ends("ation",5)) { $this->r("ate",3); break; }
                if ($this->ends("ator",4)) { $this->r("ate",3); break; }
                break;
            case 's':
                if ($this->ends("alism",5)) { $this->r("al",2); break; }
                if ($this->ends("iveness",7)) { $this->r("ive",3); break; }
                if ($this->ends("fulness",7)) { $this->r("ful",3); break; }
                if ($this->ends("ousness",7)) { $this->r("ous",3); break; }
                break;
            case 't':
                if ($this->ends("aliti",5)) { $this->r("al",2); break; }
                if ($this->ends("iviti",5)) { $this->r("ive",3); break; }
                if ($this->ends("biliti",6)) { $this->r("ble",3); break; }
                break;
            case 'g':
                if ($this->ends("logi",4)) { $this->r("log",3); break; }
                // -DEPARTURE-
                // To match the published algorithm delete the above line
        }
    }

    /*
     * step3() deals with -ic-, -full, -ness etc. similar strategy
     * to step2.
     *
     * */
    protected function step3()
    {
        switch ($this->b[$this->k]) {
            case 'e':
                if ($this->ends("icate",5)) { $this->r("ic",2); break; }
                if ($this->ends("ative",5)) { $this->r("",0); break; }
                if ($this->ends("alize",5)) { $this->r("al",2); break; }
                break;
            case 'i':
                if ($this->ends("iciti",5)) { $this->r("ic",2); break; }
                break;
            case 'l':
                if ($this->ends("ical",4)) { $this->r("ic",2); break; }
                if ($this->ends("ful",3)) { $this->r("",0); break; }
                break;
            case 's':
                if ($this->ends("ness",4)) { $this->r("",0); break; }
                break;
        }
    }

    /* step4() takes off -ant, -ence etc., in context <c>vcvc<v>. */
    protected function step4()
    {
        switch ($this->b[$this->k-1]) {
            case 'a':
                if ($this->ends("al",2))
                    break;

                return;
            case 'c':
                if ($this->ends("ance",4))
                    break;
                if ($this->ends("ence",4))
                    break;

                return;
            case 'e':
                if ($this->ends("er",2))
                    break;

                return;
            case 'i':
                if ($this->ends("ic",2))
                    break;

                return;
            case 'l':
                if ($this->ends("able",4))
                    break;
                if ($this->ends("ible",4))
                    break;

                return;
            case 'n':
                if ($this->ends("ant",3))
                    break;
                if ($this->ends("ement",5))
                    break;
                if ($this->ends("ment",4))
                    break;
                if ($this->ends("ent",3))
                    break;

                return;
            case 'o':
                if ($this->ends("ion",3) && ($this->b[$this->j] === 's' || $this->b[$this->j] === 't'))
                    break;
                if ($this->ends("ou",2))
                    break;

                return;
                /* takes care of -ous */
            case 's':
                if ($this->ends("ism",3))
                    break;

                return;
            case 't':
                if ($this->ends("ate",3))
                    break;
                if ($this->ends("iti",3))
                    break;

                return;
            case 'u':
                if ($this->ends("ous",3))
                    break;

                return;
            case 'v':
                if ($this->ends("ive",3))
                    break;

                return;
            case 'z':
                if ($this->ends("ize",3))
                    break;

                return;
            default:
                return;
        }
        if ($this->m() > 1) $this->k = $this->j;
    }

    /*
     * step5() removes a final -e if m() > 1, and
     * changes -ll to -l if m() > 1.
     *
     * */
    protected function step5()
    {
        $this->j = $this->k;
        if ($this->b[$this->k] === 'e') {
            $a = $this->m();
            if ($a > 1 || $a == 1 && !$this->cvc($this->k-1))
                $this->k--;
        }
        if ($this->b[$this->k] === 'l' && $this->doublec($this->k) && $this->m() > 1)
            $this->k--;
    }

    /**
     * The word must be a lower case one byte per character string (in
     * English).
     *
     */
    public function stem($word)
    {
        $this->j=0;
        $this->b = $word;
        $this->k = strlen($word)-1;
        if ($this->k<=1)
            return $word;

        $this->step1ab();
        $this->step1c();
        $this->step2();
        $this->step3();
        $this->step4();
        $this->step5();

        return substr($this->b,0,$this->k+1);
    }
}
