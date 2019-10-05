<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

/**
 * @template T The type of data being generated
 */
interface Set
{
    public function take(int $size): self;

    /**
     * @param callable(): bool $predicate
     */
    public function filter(callable $predicate): self;

    /**
     * @return \Generator<T>
     */
    public function values(): \Generator;
}
