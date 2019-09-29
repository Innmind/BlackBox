<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

final class Combination
{
    private $values;

    public function __construct($a, $b)
    {
        $this->values = [$a, $b];
    }

    public function add($c): self
    {
        $self = clone $this;
        \array_unshift($self->values, $c);

        return $self;
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
