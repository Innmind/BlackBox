<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Nullable
{
    /**
     * @deprecated Use $set->nulable() instead
     * @psalm-pure
     *
     * @template T
     *
     * @param Set<T>|Provider<T> $set
     *
     * @return Set<?T>
     */
    #[\NoDiscard]
    public static function of(Set|Provider $set): Set
    {
        return Collapse::of($set)->nullable();
    }
}
