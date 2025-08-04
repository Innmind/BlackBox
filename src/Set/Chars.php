<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Chars
{
    /**
     * @deprecated Use Set::strings()->chars() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public static function any(): Set
    {
        return Set::strings()
            ->chars()
            ->toSet();
    }

    /**
     * @deprecated Use Set::strings()->chars()->lowercaseLetter() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public static function lowercaseLetter(): Set
    {
        return Set::strings()
            ->chars()
            ->lowercaseLetter();
    }

    /**
     * @deprecated Use Set::strings()->chars()->uppercaseLetter() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public static function uppercaseLetter(): Set
    {
        return Set::strings()
            ->chars()
            ->uppercaseLetter();
    }

    /**
     * @deprecated Use Set::strings()->chars()->number() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public static function number(): Set
    {
        return Set::strings()
            ->chars()
            ->number();
    }

    /**
     * @deprecated Use Set::strings()->chars()->ascii() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public static function ascii(): Set
    {
        return Set::strings()
            ->chars()
            ->ascii();
    }

    /**
     * @deprecated Use Set::strings()->chars()->alphanumerical() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public static function alphanumerical(): Set
    {
        return Set::strings()
            ->chars()
            ->alphanumerical();
    }
}
