<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Seed;

use Innmind\BlackBox\Set\{
    Value,
    Dichotomy,
    Seed,
};

/**
 * @internal
 * @template T
 */
final class FlatMap
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): Seed<T> $map
     */
    private function __construct(
        private self|Map $previous,
        private \Closure $map,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param callable(): Seed<A> $map
     *
     * @return self<A>
     */
    public static function of(self|Map $previous, callable $map): self
    {
        return new self($previous, \Closure::fromCallable($map));
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
        $previous = $this->map;

        return new self(
            $this->previous,
            static fn($value) => $previous($value)->map($map),
        );
    }

    /**
     * @psalm-mutation-free
     * @template U
     *
     * @param callable(T): Seed<U> $map
     *
     * @return self<U>
     */
    public function flatMap(callable $map): self
    {
        return new self($this, \Closure::fromCallable($map));
    }

    /**
     * @psalm-mutation-free
     *
     * @param \Closure(T): bool $predicate
     */
    public function shrinkable(\Closure $predicate): bool
    {
        /** @psalm-suppress ImpureMethodCall */
        return $this->previous->shrinkable($predicate) || $this->collapse()->shrinkable($predicate);
    }

    /**
     * @param \Closure(T): bool $predicate
     *
     * @return Dichotomy<T>
     */
    public function shrink(\Closure $predicate): Dichotomy
    {
        if ($this->previous->shrinkable($predicate)) {
            return $this->previousShrink($predicate);
        }

        return $this->collapse()->shrink($predicate);
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->collapse()->unwrap();
    }

    /**
     * @return Seed<T>
     */
    private function collapse(): Seed
    {
        return ($this->map)($this->previous->unwrap());
    }

    /**
     * @param \Closure(T): bool $predicate
     *
     * @return Dichotomy<T>
     */
    private function previousShrink(\Closure $predicate): Dichotomy
    {
        $shrunk = $this->previous->shrink($predicate);

        $a = $shrunk->a();
        $b = $shrunk->b();
        $map = $this->map;

        // There's no need to define the immutability of the values here because
        // it's held by the values injected in the new Seeds.
        return new Dichotomy(
            static fn() => Value::immutable(
                Seed::of($a)->flatMap($map),
                // No dichotomy because the captured values in the configure
                // lambda is shrunk first
            )->predicatedOn($predicate),
            static fn() => Value::immutable(
                Seed::of($b)->flatMap($map),
                // No dichotomy because the captured values in the configure
                // lambda is shrunk first
            )->predicatedOn($predicate),
        );
    }
}
