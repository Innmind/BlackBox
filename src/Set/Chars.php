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
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(0, 255),
        );
    }

    /**
     * @return Set<non-empty-string>
     */
    public static function lowercaseLetter(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(97, 122),
        );
    }

    /**
     * @return Set<non-empty-string>
     */
    public static function uppercaseLetter(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(65, 90),
        );
    }

    /**
     * @return Set<non-empty-string>
     */
    public static function number(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(48, 57),
        );
    }

    /**
     * @return Set<non-empty-string>
     */
    public static function ascii(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(32, 126),
        );
    }

    /**
     * @return Set<string>
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
