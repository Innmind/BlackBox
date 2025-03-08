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
final class RecursiveNth
{
    /**
     * @internal
     * @template A
     *
     * @param callable(list<A>): bool $predicate
     * @param list<Value<A>> $sequence
     * @param positive-int $n
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function of(
        bool $mutable,
        callable $predicate,
        array $sequence,
        int $n = 1,
    ): ?Dichotomy {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!$predicate(Detonate::of($sequence))) {
            return null;
        }

        return Dichotomy::of(
            RemoveNth::of($mutable, $predicate, $sequence, $n),
            RemoveNth::of($mutable, $predicate, $sequence, $n + 1),
        );
    }
}
