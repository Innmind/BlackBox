<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class NaturalNumbersExceptZero implements Set
{
    private $set;

    public function __construct()
    {
        $this->set = Integers::of(1);
    }

    public static function of(): self
    {
        return new self;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->set = $this->set->take($size);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->set = $this->set->filter($predicate);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        return $this->set->reduce($carry, $reducer);
    }

    /**
     * @return \Generator<int>
     */
    public function values(): \Generator
    {
        yield from $this->set->values();
    }
}
