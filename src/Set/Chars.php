<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Set<string>
 */
final class Chars implements Set
{
    /** @var Set<string> */
    private Set $set;

    public function __construct()
    {
        $this->set = self::any();
    }

    /**
     * @return Set<string>
     */
    public static function any(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(0, 255),
        );
    }

    /**
     * @return Set<string>
     */
    public static function lowercaseLetter(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(97, 122),
        );
    }

    /**
     * @return Set<string>
     */
    public static function uppercaseLetter(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(65, 90),
        );
    }

    /**
     * @return Set<string>
     */
    public static function number(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(48, 57),
        );
    }

    /**
     * @return Set<string>
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
        return new Set\Either(
            self::lowercaseLetter(),
            self::uppercaseLetter(),
            self::number(),
        );
    }

    public function take(int $size): Set
    {
        return $this->set->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->set->filter($predicate);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $rand): \Generator
    {
        return $this->set->values($rand);
    }
}
