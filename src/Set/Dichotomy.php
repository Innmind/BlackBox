<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template-covariant T
 * @internal
 */
final class Dichotomy
{
    /** @var \Closure(): Value<T> */
    private \Closure $a;
    /** @var \Closure(): Value<T> */
    private \Closure $b;

    /**
     * @param callable(): Value<T> $a
     * @param callable(): Value<T> $b
     */
    private function __construct(callable $a, callable $b)
    {
        $this->a = \Closure::fromCallable($a);
        $this->b = \Closure::fromCallable($b);
    }

    /**
     * @internal
     * @template V
     *
     * @param callable(): Value<V> $a
     * @param callable(): Value<V> $b
     *
     * @return self<V>
     */
    public static function of(
        callable $a,
        callable $b,
    ): self {
        return new self($a, $b);
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
