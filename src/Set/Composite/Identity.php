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
final class Identity
{
    /**
     * @internal
     * @template A
     *
     * @param callable(...mixed): (A|Seed<A>) $aggregate
     *
     * @return callable(): Value<A>
     */
    public static function of(
        bool $mutable,
        callable $aggregate,
        Combination $combination,
    ): callable {
        return match ($mutable) {
            true => static fn() => Value::mutable(
                static fn() => $combination->detonate($aggregate),
            ),
            false => static fn() => Value::immutable(
                $combination->detonate($aggregate),
            ),
        };
    }
}
