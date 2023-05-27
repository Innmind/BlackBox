<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Strings
{
    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        return self::between(0, 128);
    }

    /**
     * @psalm-pure
     *
     * @param 0|positive-int $minLength
     * @param positive-int $maxLength
     *
     * @return Set<string>
     */
    public static function between(int $minLength, int $maxLength): Set
    {
        return Sequence::of(Chars::any())
            ->between($minLength, $maxLength)
            ->map(static fn(array $chars): string => \implode('', $chars));
    }

    /**
     * @psalm-pure
     *
     * @param positive-int $maxLength
     *
     * @return Set<string>
     */
    public static function atMost(int $maxLength): Set
    {
        return self::between(0, $maxLength);
    }

    /**
     * @psalm-pure
     *
     * @param positive-int $minLength
     *
     * @return Set<string>
     */
    public static function atLeast(int $minLength): Set
    {
        return self::between($minLength, $minLength + 128);
    }

    /**
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @param Set<string> $first
     * @param Set<string> $rest
     */
    public static function madeOf(Set $first, Set ...$rest): MadeOf
    {
        return MadeOf::of($first, ...$rest);
    }
}
