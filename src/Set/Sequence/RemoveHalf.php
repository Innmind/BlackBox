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
     * @param Value<list<Value<A>>> $value
     *
     * @return Value<list<A>>
     */
    public static function of(Value $value): Value
    {
        // we round half down otherwise a sequence of 1 element would be shrunk
        // to a sequence of 1 element resulting in a infinite recursion
        $shrunk = $value->map(static function($sequence) {
            $numberToKeep = (int) \round(\count($sequence) / 2, 0, \PHP_ROUND_HALF_DOWN);

            return \array_slice($sequence, 0, $numberToKeep);
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return RemoveTail::of($value);
        }

        return $detonated->shrinkWith(static fn() => RecursiveHalf::of($shrunk));
    }
}
