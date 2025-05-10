<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

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
     * @return \Generator<Value<T>>
     */
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator;
}
