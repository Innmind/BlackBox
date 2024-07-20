<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class RemoveTail
{
    /**
     * @internal
     * @template A
     *
     * @param callable(list<A>): bool $predicate
     * @param list<Value<A>> $sequence
     *
     * @return callable(): Value<list<A>>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        array $sequence,
    ): callable {
        $shrunk = $sequence;
        \array_pop($shrunk);

        if (!$predicate(Detonate::of($shrunk))) {
            return RemoveHead::of(
                $mutable,
                $predicate,
                $sequence,
            );
        }

        return match ($mutable) {
            true => static fn() => Value::mutable(
                static fn() => Detonate::of($shrunk),
                RecursiveTail::of($mutable, $predicate, $shrunk),
            ),
            false => static fn() => Value::immutable(
                Detonate::of($shrunk),
                RecursiveTail::of($mutable, $predicate, $shrunk),
            ),
        };
    }
}
