<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Chars
{
    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        return Set::integers()
            ->between(0, 255)
            ->toSet()
            ->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function lowercaseLetter(): Set
    {
        return Set::integers()
            ->between(97, 122)
            ->toSet()
            ->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function uppercaseLetter(): Set
    {
        return Set::integers()
            ->between(65, 90)
            ->toSet()
            ->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function number(): Set
    {
        return Set::integers()
            ->between(48, 57)
            ->toSet()
            ->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function ascii(): Set
    {
        return Set::integers()
            ->between(32, 126)
            ->toSet()
            ->map(\chr(...));
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function alphanumerical(): Set
    {
        return Set::either(
            self::lowercaseLetter(),
            self::uppercaseLetter(),
            self::number(),
        );
    }
}
