<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Value;

final class Combination
{
    private array $values;

    public function __construct(Value $right)
    {
        $this->values = [$right];
    }

    public function add(Value $left): self
    {
        $self = clone $this;
        \array_unshift($self->values, $left);

        return $self;
    }

    public function immutable(): bool
    {
        return \array_reduce(
            $this->values,
            fn(bool $immutable, Value $value): bool => $immutable && $value->isImmutable(),
            true,
        );
    }

    public function unwrap(): array
    {
        /** @psalm-suppress MissingClosureReturnType */
        return \array_map(
            static fn(Value $value) => $value->unwrap(),
            $this->values,
        );
    }
}
