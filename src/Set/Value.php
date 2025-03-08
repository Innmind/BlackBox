<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @psalm-immutable
 *
 * @template-covariant T
 */
final class Value
{
    /**
     * @param \Closure(): T $unwrap
     * @param ?Dichotomy<T> $dichotomy
     * @param \Closure(mixed): bool $predicate
     */
    private function __construct(
        private bool $immutable,
        private \Closure $unwrap,
        private ?Dichotomy $dichotomy,
        private \Closure $predicate,
    ) {
        $this->unwrap = \Closure::fromCallable($unwrap);
        $this->immutable = $immutable;
        $this->dichotomy = $dichotomy;
    }

    /**
     * @template V
     *
     * @param V $value
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
     * @template V
     *
     * @param callable(): V $unwrap
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
     * @param callable(mixed): bool $predicate
     *
     * @return self<T>
     */
    public function predicatedOn(callable $predicate): self
    {
        $previous = $this->predicate;

        return new self(
            $this->immutable,
            $this->unwrap,
            $this->dichotomy,
            static fn($value) => $previous($value) && $predicate($value),
        );
    }

    public function acceptable(): bool
    {
        /** @psalm-suppress ImpureFunctionCall */
        return ($this->predicate)($this->unwrap());
    }

    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    public function shrinkable(): bool
    {
        return $this->dichotomy instanceof Dichotomy;
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     *
     * @return Dichotomy<T>
     */
    public function shrink(): Dichotomy
    {
        /** @psalm-suppress NullableReturnStatement */
        return $this->dichotomy;
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        /** @psalm-suppress ImpureFunctionCall */
        return ($this->unwrap)();
    }
}
