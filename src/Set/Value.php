<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

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
     * @param \Closure(): (T|Seed<T>) $unwrap
     * @param \Closure(): ?Dichotomy<T> $shrink
     * @param \Closure(mixed): bool $predicate
     */
    private function __construct(
        private bool $immutable,
        private \Closure $unwrap,
        private \Closure $shrink,
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
    public static function immutable($value): self
    {
        return new self(
            true,
            static fn() => $value,
            static fn() => null,
            static fn() => true,
        );
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param callable(): (V|Seed<V>) $unwrap
     *
     * @return self<V>
     */
    public static function mutable(callable $unwrap): self
    {
        return new self(
            false,
            \Closure::fromCallable($unwrap),
            static fn() => null,
            static fn() => true,
        );
    }

    /**
     * @param \Closure(): ?Dichotomy<T> $shrink
     *
     * @return self<T>
     */
    public function shrinkWith(\Closure $shrink): self
    {
        return new self(
            $this->immutable,
            $this->unwrap,
            $shrink,
            $this->predicate,
        );
    }

    /**
     * @return self<T>
     */
    public function withoutShrinking(): self
    {
        return new self(
            $this->immutable,
            $this->unwrap,
            static fn() => null,
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
            $this->immutable,
            $this->unwrap,
            $this->shrink,
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
        $previous = $this->unwrap;
        $unwrap = static function() use ($map, $previous): mixed {
            $value = $previous();

            if ($value instanceof Seed) {
                return $value->flatMap(static function($value) use ($map) {
                    /** @var T $value */
                    $mapped = $map($value);

                    if ($mapped instanceof Seed) {
                        return $mapped;
                    }

                    return Seed::of(Value::immutable($mapped));
                });
            }

            return $map($value);
        };

        // avoid recomputing the map operation on each unwrap
        if ($this->immutable) {
            /** @psalm-suppress ImpureFunctionCall Since everything is supposed immutable this should be fine */
            $value = $unwrap();
            $unwrap = static fn(): mixed => $value;
        }

        return new self(
            $this->immutable,
            $unwrap,
            static fn() => null,
            $this->predicate,
        );
    }

    public function acceptable(): bool
    {
        return ($this->predicate)($this->unwrap());
    }

    /**
     * @psalm-mutation-free
     */
    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    /**
     * @return ?Dichotomy<T>
     */
    public function shrink(): ?Dichotomy
    {
        return ($this->shrink)() ?? $this->seed?->shrink($this->predicate);
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        $value = ($this->unwrap)();

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
