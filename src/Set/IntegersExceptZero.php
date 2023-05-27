<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class IntegersExceptZero
{
    /**
     * @psalm-pure
     *
     * @return Set<int>
     */
    public static function any(): Set
    {
        return Integers::any()->filter(static fn(int $value): bool => $value !== 0);
    }
}
