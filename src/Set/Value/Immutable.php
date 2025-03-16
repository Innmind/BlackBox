<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

use Innmind\BlackBox\Set\Seed;

/**
 * @internal
 * @template-covariant T
 */
final class Immutable
{
    /**
     * @psalm-mutation-free
     *
     * @param Map<T> $map
     * @param T|Seed<T> $unwrapped
     */
    private function __construct(
        private mixed $source,
        private Map $map,
        private mixed $unwrapped,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param V|Seed<V> $value
     *
     * @return self<V>
     */
    public static function of($value): self
    {
        /** @var self<V> */
        return new self(
            $value,
            Map::noop(),
            $value,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>|Mutable<T>
     */
    public function mutable(bool $mutable): self|Mutable
    {
        if (!$mutable) {
            return $this;
        }

        return Mutable::of(
            $this->source,
            $this->map,
        );
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
        // avoid recomputing the map operation on each unwrap
        /** @psalm-suppress ImpureMethodCall Since everything is supposed immutable this should be fine */
        $value = Map::noop()->with($map)($this->unwrapped);

        /** @var self<V> */
        return new self(
            $this->source,
            $this->map->with($map),
            $value,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): T $shrink
     *
     * @return self<T>
     */
    public function shrinkMap(callable $shrink): self
    {
        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress MixedArgument
         */
        $shrunk = $shrink($this->source);

        /** @psalm-suppress ImpureMethodCall */
        return new self(
            $shrunk,
            $this->map,
            ($this->map)($shrunk),
        );
    }

    /**
     * @return T|Seed<T>
     */
    public function unwrap()
    {
        return $this->unwrapped;
    }
}
