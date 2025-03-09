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
     *
     * @param callable(T): T $map
     *
     * @return self<T>
     */
    public function map(callable $map): self
    {
        $unwrap = $this->unwrap;

        return new self(
            $this->immutable,
            static function() use ($map, $unwrap) {
                $value = $unwrap();

                if ($value instanceof Seed) {
                    return $value->map($map);
                }

                return $map($value);
            },
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
