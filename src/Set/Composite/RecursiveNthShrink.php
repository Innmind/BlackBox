<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Seed,
    Value,
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
     * @param callable(mixed...): (A|Seed<A>) $aggregate
     * @param Value<Combination> $value
     * @param 0|positive-int $n
     *
     * @return ?Dichotomy<A>
     */
    public static function of(
        callable $aggregate,
        Value $value,
        int $n = 0,
    ): ?Dichotomy {
        $mapped = $value->map(static fn($combination) => $combination->detonate($aggregate));
        $combination = $value->unwrap();

        if (!$mapped->acceptable()) {
            return null;
        }

        return new Dichotomy(
            ShrinkANth::of($aggregate, $value, $n),
            ShrinkANth::of($aggregate, $value, $n + 1),
        );
    }
}
