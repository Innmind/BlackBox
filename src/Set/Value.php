<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set\Value\{
    Shrinker,
    End,
    Map,
};

/**
 * @internal
 * @template-covariant T
 */
final class Value
{
    /**
     * @psalm-mutation-free
     *
     * @param Map<T> $map
     * @param \Closure(mixed): bool $predicate
     * @param T $unwrapped
     */
    private function __construct(
        private mixed $source,
        private Map $map,
        private ?Shrinker $shrink,
        private \Closure $predicate,
        private mixed $unwrapped,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param V $value
     *
     * @return self<V>
     */
    public static function of($value): self
    {
        return new self(
            $value,
            Map::noop(),
            null,
            static fn() => true,
            $value,
        );
    }

    /**
     * @return self<T>
     */
    public function shrinkWith(Shrinker $shrink): self
    {
        return new self(
            $this->source,
            $this->map,
            $shrink,
            $this->predicate,
            $this->unwrapped,
        );
    }

    /**
     * @return self<T>
     */
    public function withoutShrinking(): self
    {
        return new self(
            $this->source,
            $this->map,
            null,
            $this->predicate,
            $this->unwrapped,
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
            $this->source,
            $this->map,
            $this->shrink,
            \Closure::fromCallable($predicate),
            $this->unwrapped,
        );
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param callable(T): V $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        // avoid recomputing the map operation on each unwrap
        /** @psalm-suppress ImpureMethodCall Since everything is supposed immutable this should be fine */
        $unwrapped = Map::noop()->with($map)($this->unwrapped);

        return new self(
            $this->source,
            $this->map->with($map),
            null,
            $this->predicate,
            $unwrapped,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): T $shrink
     *
     * @return self<T>
     */
    public function shrinkVia(callable $shrink): self
    {
        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress MixedArgument
         */
        $shrunk = $shrink($this->source);
        /** @psalm-suppress ImpureMethodCall */
        $unwrapped = ($this->map)($shrunk);

        return new self(
            $shrunk,
            $this->map,
            $this->shrink,
            $this->predicate,
            $unwrapped,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): (T|End|null) $shrink
     *
     * @return self<T>|End|null
     */
    public function maybeShrinkVia(callable $shrink): self|End|null
    {
        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress MixedArgument
         */
        $shrunk = $shrink($this->source);

        if (\is_null($shrunk)) {
            return null;
        }

        if ($shrunk instanceof End) {
            return $shrunk;
        }

        /** @psalm-suppress ImpureMethodCall */
        $unwrapped = ($this->map)($shrunk);

        return new self(
            $shrunk,
            $this->map,
            $this->shrink,
            $this->predicate,
            $unwrapped,
        );
    }

    public function acceptable(): bool
    {
        return ($this->predicate)($this->unwrap());
    }

    /**
     * @return ?Dichotomy<T>
     */
    public function shrink(): ?Dichotomy
    {
        if (\is_null($this->shrink)) {
            return null;
        }

        return ($this->shrink)($this)?->default($this->withoutShrinking());
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return $this->unwrapped;
    }
}
