<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class NaturalNumbersExceptZero
{
    /**
     * @deprecated Use Set::integers()->naturalNumbersExceptZero() instead
     * @psalm-pure
     *
     * @return Set<int<1, max>>
     */
    #[\NoDiscard]
    public static function any(): Set
    {
        return Set::integers()->naturalNumbersExceptZero();
    }
}
