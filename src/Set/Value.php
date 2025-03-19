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
    private ?Seed $seed = null;

    /**
     * @psalm-mutation-free
     *
     * @param Map<T> $map
     * @param Shrinker|(\Closure(self<T>): ?Dichotomy<T>) $shrink
     * @param \Closure(mixed): bool $predicate
     * @param T|Seed<T> $unwrapped
     */
    private function __construct(
        private bool $immutable,
        private mixed $source,
        private Map $map,
        private Shrinker|\Closure $shrink,
        private \Closure $predicate,
        private mixed $unwrapped,
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
            true,
            $value,
            Map::noop(),
            static fn() => null,
            static fn() => true,
            $value,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(bool $mutable): self
    {
        if (!$this->immutable) {
            // Mutable values can't become immutable
            return $this;
        }

        if (!$mutable) {
            // Already immutable
            return $this;
        }

        return new self(
            !$mutable,
            $this->source,
            $this->map,
            $this->shrink,
            $this->predicate,
            null, // no need to keep the pre-computed value when mutable
        );
    }

    /**
     * @param Shrinker|(\Closure(self<T>): ?Dichotomy<T>) $shrink
     *
     * @return self<T>
     */
    public function shrinkWith(\Closure|Shrinker $shrink): self
    {
        return new self(
            $this->immutable,
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
            $this->immutable,
            $this->source,
            $this->map,
            static fn() => null,
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
            $this->immutable,
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
     * @param callable(T): (V|Seed<V>) $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        $unwrapped = $this->unwrapped;

        if ($this->immutable) {
            // avoid recomputing the map operation on each unwrap
            /** @psalm-suppress ImpureMethodCall Since everything is supposed immutable this should be fine */
            $unwrapped = Map::noop()->with($map)($unwrapped);
        }

        return new self(
            $this->immutable,
            $this->source,
            $this->map->with($map),
            static fn() => null,
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
        $unwrapped = $this->unwrapped;

        if ($this->immutable) {
            /** @psalm-suppress ImpureMethodCall */
            $unwrapped = ($this->map)($shrunk);
        }

        return new self(
            $this->immutable,
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
     * @param callable(mixed): (mixed|End) $shrink
     *
     * @return self<T>|End|null
     */
    public function maybeShrinkVia(callable $shrink): self|End|null
    {
        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress MixedAssignment
         */
        $shrunk = $shrink($this->source);

        if (\is_null($shrunk)) {
            return null;
        }

        if ($shrunk instanceof End) {
            return $shrunk;
        }

        $unwrapped = $this->unwrapped;

        if ($this->immutable) {
            /** @psalm-suppress ImpureMethodCall */
            $unwrapped = ($this->map)($shrunk);
        }

        return new self(
            $this->immutable,
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
     * @psalm-mutation-free
     */
    public function immutable(): bool
    {
        return $this->immutable;
    }

    /**
     * @return ?Dichotomy<T>
     */
    public function shrink(): ?Dichotomy
    {
        $dichotomy = ($this->shrink)($this) ?? $this->seed?->shrink($this->predicate);

        return $dichotomy?->default($this->withoutShrinking());
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        if ($this->immutable) {
            $value = $this->unwrapped;
        } else {
            $value = ($this->map)($this->source);
        }

        // This is not ideal to hide the seeded value this way and to hijack
        // the shrinking system in self::shrinkable() and self::shrink() as it
        // complexifies the understanding of what's happening. Because now the
        // filtering can happen in 2 places.
        // Until a better idea comes along, this will stay this way.
        if ($value instanceof Seed) {
            $this->seed = $value;
            /** @var T */
            $value = $value->unwrap();
        }

        return $value;
    }
}
