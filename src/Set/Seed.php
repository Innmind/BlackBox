<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template-covariant T
 */
final class Seed
{
    /**
     * @psalm-mutation-free
     *
     * @param Seed\Map<T>|Seed\FlatMap<T> $implementation
     */
    private function __construct(
        private Seed\Map|Seed\FlatMap $implementation,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param Value<A> $value
     *
     * @return self<A>
     */
    public static function of(Value $value): self
    {
        return new self(Seed\Map::of($value));
    }

    /**
     * @psalm-mutation-free
     * @template U
     *
     * @param callable(T): U $map
     *
     * @return self<U>
     */
    public function map(callable $map): self
    {
        return new self($this->implementation->map($map));
    }

    /**
     * @psalm-mutation-free
     * @template U
     *
     * @param callable(T): self<U> $map
     *
     * @return self<U>
     */
    public function flatMap(callable $map): self
    {
        return new self($this->implementation->flatMap($map));
    }

    /**
     * @internal
     * @psalm-mutation-free
     *
     * @param \Closure(T): bool $predicate
     */
    public function shrinkable(\Closure $predicate): bool
    {
        /** @psalm-suppress ImpureMethodCall */
        return $this->implementation->shrinkable($predicate);
    }

    /**
     * @internal
     * @psalm-mutation-free
     *
     * @param \Closure(T): bool $predicate
     *
     * @return Dichotomy<T>
     */
    public function shrink(\Closure $predicate): Dichotomy
    {
        /** @psalm-suppress ImpureMethodCall */
        return $this->implementation->shrink($predicate);
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->implementation->unwrap();
    }
}
