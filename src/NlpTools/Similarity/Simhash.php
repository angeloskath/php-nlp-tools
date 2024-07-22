<?php

declare(strict_types=1);

namespace NlpTools\Similarity;

/**
 * Simhash is an implementation of the locality sensitive hash function
 * families proposed by Moses Charikar using the Earth Mover's Distance
 * http://www.cs.princeton.edu/courses/archive/spring04/cos598B/bib/CharikarEstim.pdf
 *
 * A better description of the implementation can be found at
 * http://infolab.stanford.edu/~manku/papers/07www-duplicates.pdf
 *
 * The current implementation uses md5 by default to hash the documents
 * features. Weighted features are not supported (unless duplicating a
 * feature is considered adding weight to it).
 */
class Simhash implements SimilarityInterface, DistanceInterface
{
    /**
     * This is the default hash function used to hash the members of the sets (it is just a wrapper over md5)
     *
     * @var array<int, string>
     */
    protected static array $search = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];

    /**
     * @var array<int, string>
     */
    protected static array $replace = ['0000', '0001', '0010', '0011', '0100', '0101', '0110', '0111', '1000', '1001', '1010', '1011', '1100', '1101', '1110', '1111'];

    protected static function md5(string $w): string
    {
        return str_replace(self::$search, self::$replace, md5($w));
    }

    /**
     * @param callable $h
     */
    public function __construct(protected int $length, protected $h = [self::class, 'md5'])
    {
    }

    /**
     * Compute the locality sensitive hash for this set.
     * Maintain a vector ($boxes) of length $this->length initialized to
     * 0. Each member of the set is hashed to a {$this->length} bit vector.
     * For each of these bits we either increment or decrement the
     * corresponding $boxes dimension depending on the bit being either
     * 1 or 0. Finally the signs of each dimension of the boxes vector
     * is the locality sensitive hash.
     *
     * We have departed from the original implementation at the
     * following points:
     *  1. Each feature has a weight of 1, but feature duplication is
     *     allowed.
     *
     * @param array<int|string, mixed> $set
     * @return string The bits of the hash as a string
     */
    public function simhash(array &$set): string
    {
        $boxes = array_fill(0, $this->length, 0);
        if (is_int(key($set))) {
            $dict = array_count_values($set);
        } else {
            $dict = &$set;
        }

        foreach ($dict as $m => $w) {
            $h = call_user_func($this->h, $m);
            for ($bit_idx = 0; $bit_idx < $this->length; $bit_idx++) {
                    $boxes[$bit_idx] += ($h[$bit_idx] == '1') ? $w : -$w;
            }
        }

        $s = '';
        foreach ($boxes as $box) {
            if ($box > 0) {
                $s .= '1';
            } else {
                $s .= '0';
            }
        }

        return $s;
    }

    /**
     * Computes the hamming distance of the simhashes of two sets.
     *
     * @param  array<int|string, mixed> $a Either a vector or a collection of tokens to be transformed to a vector
     * @param  array<int|string, mixed> $b Either a vector or a collection of tokens to be transformed to a vector
     */
    public function dist(array &$a, array &$b): float
    {
        $h1 = $this->simhash($a);
        $h2 = $this->simhash($b);
        $d = 0;
        for ($i = 0; $i < $this->length; $i++) {
            if ($h1[$i] !== $h2[$i]) {
                $d++;
            }
        }

        return $d;
    }

    /**
     * Computes a similarity measure from two sets. The similarity is
     * computed as 1 - (sets' distance) / (maximum possible distance).
     *
     * @param  array<int|string, mixed> $a Either a vector or a collection of tokens to be transformed to a vector
     * @param  array<int|string, mixed> $b Either a vector or a collection of tokens to be transformed to a vector
     */
    public function similarity(array &$a, array &$b): float
    {
        return ($this->length - $this->dist($a, $b)) / $this->length;
    }
}
