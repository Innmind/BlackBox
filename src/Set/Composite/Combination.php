<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

final class Combination
{
    private array $values;

    /**
     * @param mixed $right
     */
    public function __construct($right)
    {
        $this->values = [$right];
    }

    /**
     * @param mixed $left
     */
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
