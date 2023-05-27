<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

final class Chars
{
    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        return Integers::between(0, 255)->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function lowercaseLetter(): Set
    {
        return Integers::between(97, 122)->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function uppercaseLetter(): Set
    {
        return Integers::between(65, 90)->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function number(): Set
    {
        return Integers::between(48, 57)->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function ascii(): Set
    {
        return Integers::between(32, 126)->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function alphanumerical(): Set
    {
        return Set\Either::any(
            self::lowercaseLetter(),
            self::uppercaseLetter(),
            self::number(),
        );
    }
}
