<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Email
{
    /**
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        /** @var Set<string> */
        return Composite::immutable(
            static function(string $address, string $domain, string $tld): string {
                return "$address@$domain.$tld";
            },
            self::address(),
            self::domain(),
            self::tld(),
        )
            ->take(100)
            ->filter(static function(string $email): bool {
                return !\preg_match('~(\-.|\.\-)~', $email);
            });
    }

    /**
     * @return Set<string>
     */
    private static function address(): Set
    {
        return self::string(64, '-', '.', '_');
    }

    /**
     * @return Set<string>
     */
    private static function domain(): Set
    {
        return self::string(63, '-', '.');
    }

    /**
     * @return Set<string>
     */
    private static function string(int $maxLength, string ...$extra): Set
    {
        return new Set\Either(
            // either only with simple characters
            Set\Decorate::immutable(
                static fn(array $chars): string => \implode('', $chars),
                Set\Sequence::of(
                    self::letter(),
                    Set\Integers::between(1, $maxLength),
                ),
            ),
            // or with some extra ones in the middle
            Set\Composite::immutable(
                static fn(...$parts): string => \implode('', $parts),
                self::letter(),
                Set\Decorate::immutable(
                    static fn(array $chars): string => \implode('', $chars),
                    Set\Sequence::of(
                        self::letter(...$extra),
                        Set\Integers::between(1, $maxLength - 2),
                    ),
                ),
                self::letter(),
            )->filter(static function(string $string): bool {
                return !\preg_match('~\.\.~', $string);
            }),
        );
    }

    /**
     * @return Set<string>
     */
    private static function tld(): Set
    {
        return Set\Decorate::immutable(
            static fn(array $chars): string => \implode('', $chars),
            Set\Sequence::of(
                Set\Elements::of(...\range('a', 'z'), ...\range('A', 'Z')),
                Set\Integers::between(1, 63),
            ),
        );
    }

    /**
     * @return Set<string>
     */
    private static function letter(string ...$extra): Set
    {
        return Set\Elements::of(
            ...\range('a', 'z'),
            ...\range('A', 'Z'),
            ...\range(0, 9),
            ...$extra,
        );
    }
}
