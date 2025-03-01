<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class NaturalNumbers
{
    /**
     * @psalm-pure
     *
     * @return Set<0|positive-int>
     */
    public static function any(): Set
    {
        /** @var Set<0|positive-int> */
        return Set::of(Integers::above(0));
    }
}
