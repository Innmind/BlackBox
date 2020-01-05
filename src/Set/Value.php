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
        return new self(static fn() => $value);
    }

    public function isImmutable(): bool
    {
        return true;
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return ($this->unwrap)();
    }
}
