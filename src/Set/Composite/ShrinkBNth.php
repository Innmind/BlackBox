<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Value,
    Seed,
};

/**
 * @internal
 */
final class ShrinkBNth
{
    /**
     * @internal
     * @template A
     *
     * @param callable(mixed...): (A|Seed<A>) $aggregate
     * @param Value<Combination> $value
     * @param 0|positive-int $n
     *
     * @return Value<A>
     */
    public static function of(
        callable $aggregate,
        Value $value,
        int $n = 0,
    ): Value {
        $combination = $value->unwrap();
        $values = $combination->values();

        if (!\array_key_exists($n, $values)) {
            return $value
                ->map(static fn($combination) => $combination->detonate($aggregate))
                ->withoutShrinking();
        }

        $shrunk = $combination->bShrinkNth($n);

        if (\is_null($shrunk)) {
            return self::of(
                $aggregate,
                $value,
                $n + 1,
            );
        }

        $shrunk = $value->map(static fn() => $shrunk);
        $mapped = $shrunk->map(
            static fn($combination) => $combination->detonate($aggregate),
        );

        if (!$mapped->acceptable()) {
            return self::of(
                $aggregate,
                $value,
                $n + 1,
            );
        }

        return $mapped->shrinkWith(static fn() => RecursiveNthShrink::of(
            $aggregate,
            $shrunk,
            $n,
        ));
    }
}
