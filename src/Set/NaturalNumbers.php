<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class NaturalNumbers
{
    /**
     * @deprecated Use Set::integers()->naturalNumbers() instead
     * @psalm-pure
     *
     * @return Set<int<0, max>>
     */
    #[\NoDiscard]
    public static function any(): Set
    {
        return Set::integers()->naturalNumbers();
    }
}
