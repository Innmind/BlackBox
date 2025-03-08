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
final class RecursiveTail
{
    /**
     * @internal
     * @template A
     *
     * @param callable(list<A>): bool $predicate
     * @param list<Value<A>> $sequence
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        array $sequence,
    ): ?Dichotomy {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!$predicate(Detonate::of($sequence))) {
            return null;
        }

        return Dichotomy::of(
            RemoveTail::of($mutable, $predicate, $sequence),
            RemoveHead::of($mutable, $predicate, $sequence),
        );
    }
}
