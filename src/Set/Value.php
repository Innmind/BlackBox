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
     */
    private function __construct(
        private bool $immutable,
        private \Closure $unwrap,
        private ?Dichotomy $dichotomy,
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
        );
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
