<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template T
 */
final class Value
{
    /** @var \Closure(): T */
    private \Closure $unwrap;
    private bool $immutable;

    /**
     * @param callable(): T $unwrap
     */
    private function __construct(bool $immutable, callable $unwrap)
    {
        $this->unwrap = \Closure::fromCallable($unwrap);
        $this->immutable = $immutable;
    }

    /**
     * @param T $value
     */
    public static function immutable($value): self
    {
        return new self(true, static fn() => $value);
    }

    /**
     * @param callable(): T $unwrap
     */
    public static function mutable(callable $unwrap): self
    {
        return new self(false, $unwrap);
    }

    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return ($this->unwrap)();
    }
}
