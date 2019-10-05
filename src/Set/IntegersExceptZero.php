<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class IntegersExceptZero implements Set
{
    private $set;

    public function __construct()
    {
        $this->set = Integers::of()->filter(static function(int $value): bool {
            return $value !== 0;
        });
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

    public function values(): \Generator
    {
        yield from $this->set->values();
    }
}
