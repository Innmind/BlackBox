<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

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
     * @param list<Value<A>> $sequence
     *
     * @return callable(): Value<list<A>>
     */
    public static function of(bool $mutable, array $sequence): callable
    {
        return match ($mutable) {
            true => static fn() => Value::mutable(static fn() => \array_map(
                static fn($value) => $value->unwrap(),
                $sequence,
            )),
            false => static fn() => Value::immutable(\array_map(
                static fn($value) => $value->unwrap(),
                $sequence,
            )),
        };
    }
}
