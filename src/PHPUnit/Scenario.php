<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Set\Composite,
};

final class Scenario
{
    private Set $set;

    public function __construct(Set $first , Set ...$sets)
    {
        if (\count($sets) === 0) {
            $set = Set\Decorate::immutable(
                /** @psalm-suppress MissingParamType */
                static fn($value): array  => [$value],
                $first,
            );
        } else {
            $set = Set\Composite::immutable(
                /** @psalm-suppress MissingParamType */
                static fn(...$args): array => $args,
                $first,
                ...$sets,
            );
        }

        $this->set = $set->take(100);
    }

    public function take(int $size): self
    {
        $self = clone $this;
        $self->set = $this->set->take($size);

        return $self;
    }

    /**
     * @param callable(): bool $predicate
     */
    public function filter(callable $predicate): self
    {
        $self = clone $this;
        $self->set = $this->set->filter($predicate);

        return $self;
    }

    public function then(callable $test): void
    {
        foreach ($this->set->values() as $values) {
            $test(...$values->unwrap());
        }
    }
}
