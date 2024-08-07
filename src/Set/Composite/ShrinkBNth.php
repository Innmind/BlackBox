<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class ShrinkBNth
{
    /**
     * @internal
     * @template A
     *
     * @param callable(A): bool $predicate
     * @param callable(mixed...): A $aggregate
     * @param 0|positive-int $n
     *
     * @return callable(): Value<A>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        callable $aggregate,
        Combination $combination,
        int $n = 0,
    ): callable {
        $values = $combination->values();

        if (!\array_key_exists($n, $values)) {
            return Identity::of(
                $mutable,
                $aggregate,
                $combination,
            );
        }

        if (!$values[$n]->shrinkable()) {
            return self::of(
                $mutable,
                $predicate,
                $aggregate,
                $combination,
                $n + 1,
            );
        }

        $shrunk = $combination->bShrinkNth($n);

        if (!$predicate($shrunk->detonate($aggregate))) {
            return self::of(
                $mutable,
                $predicate,
                $aggregate,
                $combination,
                $n + 1,
            );
        }

        return match ($mutable) {
            true => static fn() => Value::mutable(
                static fn() => $shrunk->detonate($aggregate),
                RecursiveNthShrink::of(
                    $mutable,
                    $predicate,
                    $aggregate,
                    $shrunk,
                    $n,
                ),
            ),
            false => static fn() => Value::immutable(
                $shrunk->detonate($aggregate),
                RecursiveNthShrink::of(
                    $mutable,
                    $predicate,
                    $aggregate,
                    $shrunk,
                    $n,
                ),
            ),
        };
    }
}
