<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * Use this set to prevent iterating over all possible combinations of a composite set
 *
 * It will allow to test more diverse combinations for a given set
 */
final class Randomize implements Set
{
    private Set $set;
    private int $size;

    public function __construct(Set $set)
    {
        $this->set = $set;
        $this->size = 100;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        return new self($this->set->filter($predicate));
    }

    public function values(): \Generator
    {
        for ($i = 0; $i < $this->size; $i++) {
            yield $this->set->values()->current();
        }
    }
}
