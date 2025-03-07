<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Seed,
};

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
     * @param callable(mixed...): (A|Seed<A>) $aggregate
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
        $value = $combination->detonate($aggregate);

        if ($value instanceof Seed) {
            /** @var A */
            $value = $value->unwrap();
        }

        if (!$predicate($value)) {
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
