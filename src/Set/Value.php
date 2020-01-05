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
    private bool $immutable = false;

    /**
     * @param callable(): T $unwrap
     */
    private function __construct(callable $unwrap)
    {
        $this->unwrap = \Closure::fromCallable($unwrap);
    }

    /**
     * @param T $value
     */
    public static function immutable($value): self
    {
        $self = new self(static fn() => $value);
        $self->immutable = true;

        return $self;
    }

    /**
     * @param callable(): T $unwrap
     */
    public static function mutable(callable $unwrap): self
    {
        return new self($unwrap);
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
