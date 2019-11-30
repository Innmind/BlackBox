<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<int>
 */
final class NaturalNumbersExceptZero implements Set
{
    /** @var Set<int> */
    private Set $set;

    public function __construct()
    {
        $this->set = Integers::above(1);
    }

    public static function any(): self
    {
        return new self;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->set = $this->set->take($size);

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->set = $this->set->filter($predicate);

        return $self;
    }

    public function values(): \Generator
    {
        yield from $this->set->values();
    }
}
