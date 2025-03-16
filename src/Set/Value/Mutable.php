<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

use Innmind\BlackBox\Set\Seed;

/**
 * @internal
 * @template-covariant T
 */
final class Mutable
{
    /**
     * @psalm-mutation-free
     *
     * @param Map<T> $map
     */
    private function __construct(
        private mixed $source,
        private Map $map,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param V|Seed<V> $value
     * @param Map<V> $map
     *
     * @return self<V>
     */
    public static function of($value, Map $map): self
    {
        return new self($value, $map);
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(bool $mutable): self
    {
        // Mutable values can't become immutable
        return $this;
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param callable(T): (V|Seed<V>) $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        return new self(
            $this->source,
            $this->map->with($map),
        );
    }

    /**
     * @return T|Seed<T>
     */
    public function unwrap()
    {
        return ($this->map)($this->source);
    }
}
