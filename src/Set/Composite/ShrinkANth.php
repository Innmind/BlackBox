<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Value,
    Seed,
};

/**
 * @internal
 */
final class ShrinkANth
{
    /**
     * @internal
     * @template A
     *
     * @param callable(A): bool $predicate
     * @param callable(mixed...): (A|Seed<A>) $aggregate
     * @param 0|positive-int $n
     *
     * @return callable(): Value<A>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        callable $aggregate,
        Combination $combination,
        int $n,
    ): callable {
        $values = $combination->values();

        if (!\array_key_exists($n, $values)) {
            return ShrinkBNth::of(
                $mutable,
                $predicate,
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

        $shrunk = $combination->aShrinkNth($n);
        $value = $shrunk->detonate($aggregate);

        if ($value instanceof Seed) {
            /** @var A */
            $value = $value->unwrap();
        }

        if (!$predicate($value)) {
            return self::of(
                $mutable,
                $predicate,
                $aggregate,
                $combination,
                $n + 1,
            );
        }

        return match ($mutable) {
            true => static fn() => Value::mutable(static fn() => $shrunk->detonate($aggregate))
                ->predicatedOn($predicate)
                ->shrinkWith(RecursiveNthShrink::of(
                    $mutable,
                    $predicate,
                    $aggregate,
                    $shrunk,
                    $n,
                )),
            false => static fn() => Value::immutable($shrunk->detonate($aggregate))
                ->predicatedOn($predicate)
                ->shrinkWith(RecursiveNthShrink::of(
                    $mutable,
                    $predicate,
                    $aggregate,
                    $shrunk,
                    $n,
                )),
        };
    }
}
