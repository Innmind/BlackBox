<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Set\Composite,
    Set\Randomize,
};

final class Scenario
{
    private \Closure $expectsException;
    private Set $set;
    /** @var \Closure(Set): Set */
    private \Closure $wrap;
    private TestRunner $run;

    /**
     * @param callable(\Throwable): bool $expectsException
     */
    public function __construct(callable $expectsException, Set $first , Set ...$sets)
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

        $this->expectsException = \Closure::fromCallable($expectsException);
        $this->set = $set->take(100);
        $this->wrap = \Closure::fromCallable(static fn(Set $set): Set => new Randomize($set));
        $this->run = new TestRunner($expectsException);
    }

    public function take(int $size): self
    {
        $wrap = $this->wrap;
        $self = clone $this;
        $self->set = $this->set->take($size);
        $self->wrap = \Closure::fromCallable(
            static fn(Set $set): Set => $wrap($set)->take($size),
        );

        return $self;
    }

    public function disableShrinking(): self
    {
        $self = clone $this;
        $self->run = new TestRunner($this->expectsException, true);

        return $self;
    }

    /**
     * @param callable(mixed...): bool $predicate
     */
    public function filter(callable $predicate): self
    {
        $self = clone $this;
        $self->set = $this->set->filter(
            static fn(array $args): bool => $predicate(...$args),
        );

        return $self;
    }

    public function then(callable $test): void
    {
        $set = ($this->wrap)($this->set);

        foreach ($set->values() as $values) {
            ($this->run)($test, $values);
        }
    }
}
