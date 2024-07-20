<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 */
final class RecursiveNthShrink
{
    /**
     * @internal
     * @template A
     *
     * @param callable(list<A>): bool $predicate
     * @param list<Value<A>> $sequence
     * @param 0|positive-int $n
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        array $sequence,
        int $n = 0,
    ): ?Dichotomy {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!$predicate(Detonate::of($sequence))) {
            return null;
        }

        return new Dichotomy(
            ShrinkANth::of($mutable, $predicate, $sequence, $n),
            ShrinkANth::of($mutable, $predicate, $sequence, $n + 1),
        );
    }
}
