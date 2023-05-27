<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @psalm-immutable
 *
 * @template T
 */
final class Value
{
    /** @var \Closure(): T */
    private \Closure $unwrap;
    private bool $immutable;
    /** @var ?Dichotomy<T> */
    private ?Dichotomy $dichotomy;

    /**
     * @param callable(): T $unwrap
     * @param ?Dichotomy<T> $dichotomy
     */
    private function __construct(
        bool $immutable,
        callable $unwrap,
        ?Dichotomy $dichotomy,
    ) {
        $this->unwrap = \Closure::fromCallable($unwrap);
        $this->immutable = $immutable;
        $this->dichotomy = $dichotomy;
    }

    /**
     * @template V
     *
     * @param V $value
     * @param Dichotomy<V>|null $dichotomy
     *
     * @return self<V>
     */
    public static function immutable($value, Dichotomy $dichotomy = null): self
    {
        return new self(true, static fn() => $value, $dichotomy);
    }

    /**
     * @template V
     *
     * @param callable(): V $unwrap
     * @param Dichotomy<V>|null $dichotomy
     *
     * @return self<V>
     */
    public static function mutable(callable $unwrap, Dichotomy $dichotomy = null): self
    {
        return new self(false, $unwrap, $dichotomy);
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
