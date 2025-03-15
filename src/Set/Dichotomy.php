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
     * @param Value<T> $a
     * @param Value<T> $b
     */
    private function __construct(
        private Value $a,
        private Value $b,
    ) {
    }

    /**
     * @internal
     * @template V
     *
     * @param Value<V> $a
     * @param Value<V> $b
     *
     * @return self<V>
     */
    public static function of(Value $a, Value $b): self
    {
        return new self($a, $b);
    }

    /**
     * @return Value<T>
     */
    public function a(): Value
    {
        return $this->a;
    }

    /**
     * @return Value<T>
     */
    public function b(): Value
    {
        return $this->b;
    }
}
