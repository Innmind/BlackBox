<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class RemoveHalf
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
        // we round half down otherwise a sequence of 1 element would be shrunk
        // to a sequence of 1 element resulting in a infinite recursion
        $numberToKeep = (int) \round(\count($sequence) / 2, 0, \PHP_ROUND_HALF_DOWN);
        $shrunk = \array_slice($sequence, 0, $numberToKeep);

        if (!$predicate(Detonate::of($shrunk))) {
            return RemoveTail::of(
                $mutable,
                $predicate,
                $sequence,
            );
        }

        return match ($mutable) {
            true => static fn() => Value::mutable(static fn() => Detonate::of($shrunk))
                ->shrinkWith(RecursiveHalf::of(
                    $mutable,
                    $predicate,
                    $shrunk,
                )),
            false => static fn() => Value::immutable(Detonate::of($shrunk))
                ->shrinkWith(RecursiveHalf::of(
                    $mutable,
                    $predicate,
                    $shrunk,
                )),
        };
    }
}
