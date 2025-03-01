<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Nullable
{
    /**
     * @psalm-pure
     *
     * @template T
     *
     * @param Set<T>|Provider<T> $set
     *
     * @return Set<?T>
     */
    public static function of(Set|Provider $set): Set
    {
        return Collapse::of($set)->nullable();
    }
}
