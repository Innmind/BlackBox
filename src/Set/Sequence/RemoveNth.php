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
     * @param callable(list<A>): bool $predicate
     * @param list<Value<A>> $sequence
     * @param positive-int $n
     *
     * @return callable(): Value<list<A>>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        array $sequence,
        int $n = 1,
    ): callable {
        if (!\array_key_exists($n, $sequence)) {
            return ShrinkANth::of($mutable, $predicate, $sequence);
        }

        $shrunk = [];

        foreach ($sequence as $i => $value) {
            if ($i !== $n) {
                $shrunk[] = $value;
            }
        }

        if (!$predicate(Detonate::of($shrunk))) {
            return ShrinkANth::of(
                $mutable,
                $predicate,
                $sequence,
            );
        }

        return match ($mutable) {
            true => static fn() => Value::mutable(static fn() => Detonate::of($shrunk))
                ->shrinkWith(RecursiveNth::of($mutable, $predicate, $shrunk, $n)),
            false => static fn() => Value::immutable(Detonate::of($shrunk))
                ->shrinkWith(RecursiveNth::of($mutable, $predicate, $shrunk, $n)),
        };
    }
}
