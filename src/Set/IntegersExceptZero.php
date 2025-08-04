<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class IntegersExceptZero
{
    /**
     * @deprecated Use Set::integers()->exceptZero() instead
     * @psalm-pure
     *
     * @return Set<int>
     */
    #[\NoDiscard]
    public static function any(): Set
    {
        return Set::integers()->exceptZero();
    }
}
