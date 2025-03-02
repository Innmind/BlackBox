<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Strings
{
    /**
     * @deprecated Use Set::strings() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        return Set::strings()->toSet();
    }

    /**
     * @deprecated Use Set::strings()->between() instead
     * @psalm-pure
     *
     * @param 0|positive-int $min
     * @param positive-int $max
     *
     * @return Set<string>
     */
    public static function between(int $min, int $max): Set
    {
        return Set::strings()->between($min, $max);
    }

    /**
     * @deprecated Use Set::strings()->atMost() instead
     * @psalm-pure
     *
     * @param positive-int $max
     *
     * @return Set<string>
     */
    public static function atMost(int $max): Set
    {
        return Set::strings()->atMost($max);
    }

    /**
     * @deprecated Use Set::strings()->atLeast() instead
     * @psalm-pure
     *
     * @param positive-int $min
     *
     * @return Set<string>
     */
    public static function atLeast(int $min): Set
    {
        return Set::strings()->atLeast($min);
    }

    /**
     * @deprecated Use Set::strings()->madeOf() instead
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @param Set<string>|Provider<string> $first
     * @param Set<string>|Provider<string> $rest
     */
    public static function madeOf(Set|Provider $first, Set|Provider ...$rest): MadeOf
    {
        return MadeOf::of($first, ...$rest);
    }
}
