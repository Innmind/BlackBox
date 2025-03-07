<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * @internal
 * @template-covariant T The type of data being generated
 */
interface Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     *
     * @return self<T>
     */
    public function take(int $size): self;

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): bool $predicate
     *
     * @return self<T>
     */
    public function filter(callable $predicate): self;

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(T): (Seed<V>|V) $map
     *
     * @return self<V>
     */
    public function map(callable $map): self;

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(Seed<T>): (Set<V>|Provider<V>) $map
     * @param callable(Set<V>|Provider<V>): self<V> $extract
     *
     * @return self<V>
     */
    public function flatMap(callable $map, callable $extract): self;

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return \Generator<Value<T>>
     */
    public function values(Random $random): \Generator;
}
