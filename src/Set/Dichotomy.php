<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template T
 * @internal
 */
final class Dichotomy
{
    /**
     * @param \Closure(): Value<T> $a
     * @param \Closure(): Value<T> $b
     */
    private function __construct(
        private \Closure $a,
        private \Closure $b,
    ) {
    }

    /**
     * @internal
     * @template A
     *
     * @param callable(): Value<A> $a
     * @param callable(): Value<A> $b
     *
     * @return self<A>
     */
    public static function of(callable $a, callable $b)
    {
        return new self(\Closure::fromCallable($a), \Closure::fromCallable($b));
    }

    /**
     * @return Value<T>
     */
    public function a(): Value
    {
        return ($this->a)();
    }

    /**
     * @return Value<T>
     */
    public function b(): Value
    {
        return ($this->b)();
    }
}
