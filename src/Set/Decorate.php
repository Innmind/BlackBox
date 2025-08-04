<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @internal
 */
final class Decorate
{
    /**
     * @deprecated Use $set->map() instead
     * @psalm-pure
     *
     * @template T
     * @template V
     *
     * @param callable(V): T $decorate It must be a pure function (no randomness, no side effects)
     * @param Set<V>|Provider<V> $set
     *
     * @return Set<T>
     */
    #[\NoDiscard]
    public static function immutable(callable $decorate, Set|Provider $set): Set
    {
        return Collapse::of($set)->map($decorate);
    }

    /**
     * @deprecated Use Set::decorate() instead
     * @psalm-pure
     *
     * @template T
     * @template V
     *
     * @param callable(V): T $decorate It must be a pure function (no randomness, no side effects)
     * @param Set<V>|Provider<V> $set
     *
     * @return Set<T>
     */
    #[\NoDiscard]
    public static function mutable(callable $decorate, Set|Provider $set): Set
    {
        return Set::decorate($decorate, $set);
    }
}
