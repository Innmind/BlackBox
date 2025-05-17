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
     *
     * @return \Generator<Value<T>>
     */
    public function __invoke(
        Random $random,
        // The predicate is still sent through the composition stack as it
        // allows implementations like `Elements` to rule out values from ever
        // being generated as these would never match the predicate when verified
        // above in the stack.
        \Closure $predicate,
    ): \Generator;
}
