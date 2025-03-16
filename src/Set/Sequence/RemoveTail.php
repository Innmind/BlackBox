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
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Value<list<A>>
     */
    public static function of(Value $value): ?Value
    {
        $shrunk = $value->map(static function($sequence) {
            $shrunk = $sequence;
            \array_pop($shrunk);

            return $shrunk;
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return RemoveHead::of($value);
        }

        return $detonated->shrinkWith(static fn() => RecursiveTail::of($shrunk));
    }
}
