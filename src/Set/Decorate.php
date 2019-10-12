<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class Decorate implements Set
{
    private $decorate;
    private $set;

    public function __construct(
        callable $decorate,
        Set $set
    ) {
        $this->decorate = $decorate;
        $this->set = $set;
    }

    public static function of(
        callable $decorate,
        Set $set
    ): self {
        return new self($decorate, $set);
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
    public function values(): \Generator
    {
        foreach ($this->set->values() as $value) {
            yield ($this->decorate)($value);
        }
    }
}
