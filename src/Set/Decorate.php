<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Decorate implements Set
{
    private \Closure $decorate;
    private Set $set;

    public function __construct(
        callable $decorate,
        Set $set
    ) {
        $this->decorate = \Closure::fromCallable($decorate);
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

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->set = $this->set->filter($predicate);

        return $self;
    }

    public function values(): \Generator
    {
        /** @var mixed */
        foreach ($this->set->values() as $value) {
            yield ($this->decorate)($value);
        }
    }
}
