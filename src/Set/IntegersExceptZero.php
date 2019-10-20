<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class IntegersExceptZero implements Set
{
    private $set;

    public function __construct()
    {
        $this->set = Integers::of()->filter(static function(int $value): bool {
            return $value !== 0;
        });
    }

    public static function any(): self
    {
        return new self;
    }

    /**
     * @deprecated
     * @see self::any()
     */
    public static function of(): self
    {
        return self::any();
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
     * @return \Generator<int>
     */
    public function values(): \Generator
    {
        yield from $this->set->values();
    }
}
