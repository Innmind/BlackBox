<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class ShrinkANth
{
    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param 0|positive-int $n
     *
     * @return callable(): Value<list<A>>
     */
    public static function of(Value $value, int $n = 0): callable
    {
        $sequence = $value->unwrap();

        if (!\array_key_exists($n, $sequence)) {
            return ShrinkBNth::of($value);
        }

        $nShrunk = $sequence[$n]->shrink();

        if (\is_null($nShrunk)) {
            return self::of($value, $n + 1);
        }

        $shrunk = $value->map(static function($sequence) use ($n, $nShrunk) {
            $sequence[$n] = $nShrunk->a();

            return \array_values($sequence);
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::of($value, $n + 1);
        }

        return static fn() => $detonated->shrinkWith(static fn() => RecursiveNthShrink::of(
            $shrunk,
            $n,
        ));
    }
}
