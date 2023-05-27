<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class NaturalNumbersExceptZero
{
    /**
     * @return Set<positive-int>
     */
    public static function any(): Set
    {
        /** @var Set<positive-int> */
        return Integers::above(1);
    }
}
