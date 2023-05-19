<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Nullable
{
    /**
     * @template T
     *
     * @param Set<T> $set
     *
     * @return Set<?T>
     */
    public static function of(Set $set): Set
    {
        return Either::any(
            $set,
            Elements::of(null),
        );
    }
}
