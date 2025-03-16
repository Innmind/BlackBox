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
     * @param \Closure(mixed): bool $predicate
     */
    private function __construct(
        private Value\Immutable|Value\Mutable $implementation,
        private \Closure $predicate,
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
        return new self(
            Value\Immutable::of($value),
            static fn() => true,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(bool $mutable): self
    {
        return new self(
            $this->implementation->mutable($mutable),
            $this->predicate,
        );
    }

    /**
     * @param \Closure(self<T>): ?Dichotomy<T> $shrink
     *
     * @return self<T>
     */
    public function shrinkWith(\Closure $shrink): self
    {
        return new self(
            $this->implementation->shrinkWith($shrink),
            $this->predicate,
        );
    }

    /**
     * @return self<T>
     */
    public function withoutShrinking(): self
    {
        return new self(
            $this->implementation->withoutShrinking(),
            $this->predicate,
        );
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
        return new self(
            $this->implementation->predicatedOn($predicate),
            \Closure::fromCallable($predicate),
        );
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
        return new self(
            $this->implementation->map($map),
            $this->predicate,
        );
    }

    public function acceptable(): bool
    {
        return ($this->predicate)($this->implementation->unwrap());
    }

    /**
     * @psalm-mutation-free
     */
    public function immutable(): bool
    {
        return $this->implementation instanceof Value\Immutable;
    }

    /**
     * @return ?Dichotomy<T>
     */
    public function shrink(): ?Dichotomy
    {
        $predicate = $this->predicate;

        return $this->implementation->shrink(
            static fn($implementation) => new self(
                $implementation,
                $predicate,
            ),
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
