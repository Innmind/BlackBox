<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Set<int>
 */
final class NaturalNumbers implements Set
{
    /** @var Set<int> */
    private Set $set;

    public function __construct()
    {
        $this->set = Integers::above(0);
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

    public function values(Random $rand): \Generator
    {
        yield from $this->set->values($rand);
    }
}
