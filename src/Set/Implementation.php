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
     * @psalm-suppress InvalidTemplateParam
     *
     * @param \Closure(T): bool $predicate
     * @param int<1, max> $size
     *
     * @throws EmptySet When no value can be generated
     *
     * @return \Generator<Value<T>>
     */
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator;
}
