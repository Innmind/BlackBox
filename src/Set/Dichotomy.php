<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template-covariant T
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
        return new self(
            \Closure::fromCallable($a),
            \Closure::fromCallable($b),
        );
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
