<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @internal
 * @template-covariant T
 */
final class Value
{
    /**
     * @psalm-mutation-free
     *
     * @param Value\Immutable<T>|Value\Mutable<T> $implementation
     */
    private function __construct(
        private Value\Immutable|Value\Mutable $implementation,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param V|Seed<V> $value
     *
     * @return self<V>
     */
    public static function of($value): self
    {
        return new self(Value\Immutable::of($value));
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(bool $mutable): self
    {
        return new self($this->implementation->mutable($mutable));
    }

    /**
     * @param \Closure(self<T>): ?Dichotomy<T> $shrink
     *
     * @return self<T>
     */
    public function shrinkWith(\Closure $shrink): self
    {
        return new self($this->implementation->shrinkWith($shrink));
    }

    /**
     * @return self<T>
     */
    public function withoutShrinking(): self
    {
        return new self($this->implementation->withoutShrinking());
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(mixed): bool $predicate
     *
     * @return self<T>
     */
    public function predicatedOn(callable $predicate): self
    {
        return new self($this->implementation->predicatedOn($predicate));
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param callable(T): (V|Seed<V>) $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        return new self($this->implementation->map($map));
    }

    public function acceptable(): bool
    {
        return $this->implementation->acceptable();
    }

    /**
     * @psalm-mutation-free
     */
    public function immutable(): bool
    {
        return $this->implementation->immutable();
    }

    /**
     * @return ?Dichotomy<T>
     */
    public function shrink(): ?Dichotomy
    {
        return $this->implementation->shrink(
            static fn($implementation) => new self($implementation),
        );
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return $this->implementation->unwrap();
    }
}
