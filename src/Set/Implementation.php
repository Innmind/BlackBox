<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Random,
    Exception\EmptySet,
};

/**
 * @internal
 * @template T The type of data being generated
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
     * @param callable(T): V $map
     *
     * @return self<V>
     */
    public function map(callable $map): self;

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return \Generator<Value<T>>
     */
    public function values(Random $random): \Generator;
}
