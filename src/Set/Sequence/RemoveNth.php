<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class RemoveNth
{
    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param positive-int $n
     *
     * @return callable(): Value<list<A>>
     */
    public static function of(Value $value, int $n = 1): callable
    {
        if (!\array_key_exists($n, $value->unwrap())) {
            return ShrinkANth::of($value);
        }

        $shrunk = $value->map(static function($sequence) use ($n) {
            $shrunk = [];

            foreach ($sequence as $i => $value) {
                if ($i !== $n) {
                    $shrunk[] = $value;
                }
            }

            return $shrunk;
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return ShrinkANth::of($value);
        }

        return static fn() => $detonated->shrinkWith(static fn() => RecursiveNth::of(
            $shrunk,
            $n,
        ));
    }
}
