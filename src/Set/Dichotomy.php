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
     * @param ?Value<T> $a
     * @param ?Value<T> $b
     */
    private function __construct(
        private ?Value $a,
        private ?Value $b,
    ) {
    }

    /**
     * @internal
     * @template V
     *
     * @param ?Value<V> $a
     * @param ?Value<V> $b
     *
     * @return ?self<V>
     */
    public static function of(?Value $a, ?Value $b): ?self
    {
        if (\is_null($a) && \is_null($b)) {
            return null;
        }

        return new self($a ?? $b, $b);
    }

    /**
     * @psalm-mutation-free
     *
     * @param Value<T> $default
     *
     * @return self<T>
     */
    public function default(Value $default): self
    {
        return new self(
            $this->a ?? $default,
            $this->b ?? $default,
        );
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param pure-callable(Value<T>): Value<V> $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        return new self(
            match ($this->a) {
                null => null,
                default => $map($this->a),
            },
            match ($this->b) {
                null => null,
                default => $map($this->b),
            },
        );
    }

    /**
     * @return Value<T>
     */
    public function a(): Value
    {
        return $this->a ?? throw new \LogicException('Default value missing');
    }

    /**
     * @return Value<T>
     */
    public function b(): Value
    {
        return $this->b ?? throw new \LogicException('Default value missing');
    }
}
