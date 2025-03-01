<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @internal
 */
final class Collapse
{
    /**
     * @template T
     * @psalm-pure
     *
     * @param Set<T>|Provider<T> $set
     *
     * @return Set<T>
     */
    public static function of(Set|Provider $set): Set
    {
        if ($set instanceof Provider) {
            return $set->toSet();
        }

        return $set;
    }
}
