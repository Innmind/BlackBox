<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Set\Value;

/**
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
     * @internal End users mustn't use this method directly (BC breaks may be introduced)
     *
     * @return \Generator<Value<T>>
     */
    public function values(Random $rand): \Generator;
}
