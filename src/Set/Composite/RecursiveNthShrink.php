<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Dichotomy;

/**
 * @internal
 */
final class RecursiveNthShrink
{
    /**
     * @internal
     * @template A
     *
     * @param callable(A): bool $predicate
     * @param callable(mixed...): A $aggregate
     * @param 0|positive-int $n
     *
     * @return ?Dichotomy<A>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        callable $aggregate,
        Combination $combination,
        int $n = 0,
    ): ?Dichotomy {
        if (!$predicate($aggregate(...$combination->unwrap()))) {
            return null;
        }

        if (!$combination->shrinkable()) {
            return null;
        }

        return new Dichotomy(
            ShrinkANth::of($mutable, $predicate, $aggregate, $combination, $n),
            ShrinkANth::of($mutable, $predicate, $aggregate, $combination, $n + 1),
        );
    }
}
