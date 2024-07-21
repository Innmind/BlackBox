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
     * @param callable(list<A>): bool $predicate
     * @param list<Value<A>> $sequence
     * @param 0|positive-int $n
     *
     * @return callable(): Value<list<A>>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        array $sequence,
        int $n = 0,
    ): callable {
        if (!\array_key_exists($n, $sequence)) {
            return ShrinkBNth::of($mutable, $predicate, $sequence);
        }

        if (!$sequence[$n]->shrinkable()) {
            return self::of($mutable, $predicate, $sequence, $n + 1);
        }

        $shrunk = [];

        foreach ($sequence as $i => $value) {
            if ($i === $n) {
                $value = $value->shrink()->a();
            }

            $shrunk[] = $value;
        }

        if (!$predicate(Detonate::of($shrunk))) {
            return self::of($mutable, $predicate, $sequence, $n + 1);
        }

        return match ($mutable) {
            true => static fn() => Value::mutable(
                static fn() => Detonate::of($shrunk),
                RecursiveNthShrink::of($mutable, $predicate, $shrunk, $n),
            ),
            false => static fn() => Value::immutable(
                Detonate::of($shrunk),
                RecursiveNthShrink::of($mutable, $predicate, $shrunk, $n),
            ),
        };
    }
}
