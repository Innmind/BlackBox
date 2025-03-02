<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * Use this set to prove your code is indifferent to the value passed to it
 */
final class Type
{
    /**
     * @deprecated Use Set::type() instead
     * @return Set<mixed>
     */
    public static function any(): Set
    {
        return Set::type();
    }
}
