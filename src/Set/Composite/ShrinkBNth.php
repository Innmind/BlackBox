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
     * @return callable(): Value<A>
     */
    public static function of(
        callable $aggregate,
        Value $value,
        int $n = 0,
    ): callable {
        $combination = $value->unwrap();
        $values = $combination->values();

        if (!\array_key_exists($n, $values)) {
            return static fn() => $value
                ->map(static fn($combination) => $combination->detonate($aggregate))
                ->withoutShrinking();
        }

        if (!$values[$n]->shrinkable()) {
            return self::of(
                $aggregate,
                $value,
                $n + 1,
            );
        }

        $shrunk = $value->map(static fn($combination) => $combination->bShrinkNth($n));
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

        return static fn() => $mapped->shrinkWith(RecursiveNthShrink::of(
            $aggregate,
            $shrunk,
            $n,
        ));
    }
}
