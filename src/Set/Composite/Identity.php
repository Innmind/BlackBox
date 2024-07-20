<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class Identity
{
    /**
     * @internal
     * @template A
     *
     * @param callable(...mixed): A $aggregate
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
                static fn() => $aggregate(...$combination->unwrap()),
            ),
            false => static fn() => Value::immutable(
                $aggregate(...$combination->unwrap()),
            ),
        };
    }
}
