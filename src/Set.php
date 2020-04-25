<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Set\Value;

/**
 * @internal End users mustn't create implementations of this interface (BC breaks may be introduced)
 * @template T The type of data being generated
 */
interface Set
{
    /**
     * @return self<T>
     */
    public function take(int $size): self;

    /**
     * @param callable(T): bool $predicate
     *
     * @return self<T>
     */
    public function filter(callable $predicate): self;

    /**
     * @return \Generator<Value<T>>
     */
    public function values(): \Generator;
}
