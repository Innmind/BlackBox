<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

final class Combination
{
    private $values;

    public function __construct($right)
    {
        $this->values = [$right];
    }

    public function add($left): self
    {
        $self = clone $this;
        \array_unshift($self->values, $left);

        return $self;
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
