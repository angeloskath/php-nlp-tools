<?php

declare(strict_types=1);

namespace NlpTools\Random\Distributions;

use NlpTools\Random\Distributions\Gamma;
use NlpTools\Random\Generators\GeneratorInterface;

/**
 * Implement a k-dimensional Dirichlet distribution using draws from
 * k gamma distributions and then normalizing.
 */
class Dirichlet extends AbstractDistribution
{
    /**
     * @var array<int, Gamma>
     */
    protected array $gamma;

    public function __construct(mixed $a, float $k, GeneratorInterface $generator = null)
    {
        parent::__construct($generator);

        $k = (int) abs($k);
        if (!is_array($a)) {
            $a = array_fill_keys(range(0, $k - 1), $a);
        }

        $generator = $this->rnd;
        $this->gamma = array_map(
            fn($a): Gamma => new Gamma($a, 1, $generator),
            $a
        );
    }

    /**
     * @return array<int, float>
     */
    public function sample(): array
    {
        $y = [];
        /** @var Gamma $g */
        foreach ($this->gamma as $g) {
            $y[] = $g->sample();
        }

        $sum = array_sum($y);

        return array_map(
            fn($y): float => $y / $sum,
            $y
        );
    }
}
