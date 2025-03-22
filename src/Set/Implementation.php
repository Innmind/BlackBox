<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
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
     * @psalm-suppress InvalidTemplateParam
     *
     * @param \Closure(T): bool $predicate
     * @param int<1, max> $size
     *
     * @throws EmptySet When no value can be generated
     *
     * @return \Generator<Value<T>>
     */
    public function values(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator;
}
