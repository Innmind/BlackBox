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
     * @param ?Dichotomy<T> $dichotomy
     * @param \Closure(mixed): bool $predicate
     */
    private function __construct(
        private bool $immutable,
        private \Closure $unwrap,
        private ?Dichotomy $dichotomy,
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
            null,
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
            null,
            static fn() => true,
        );
    }

    /**
     * @param ?Dichotomy<T> $dichotomy
     *
     * @return self<T>
     */
    public function shrinkWith(?Dichotomy $dichotomy): self
    {
        return new self(
            $this->immutable,
            $this->unwrap,
            $dichotomy,
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
            null,
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
            $this->dichotomy,
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
            null,
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

    public function shrinkable(): bool
    {
        return $this->dichotomy instanceof Dichotomy || ($this->seed?->shrinkable() === true);
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     *
     * @return Dichotomy<T>
     */
    public function shrink(): Dichotomy
    {
        /** @psalm-suppress NullableReturnStatement */
        return $this->dichotomy ?? $this->seed?->shrink();
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        $value = ($this->unwrap)();

        if ($value instanceof Seed) {
            $this->seed = $value;
            /** @var T */
            $value = $value->unwrap();
        }

        return $value;
    }
}
