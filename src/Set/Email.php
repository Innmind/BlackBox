<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Email
{
    /**
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        /** @var Set<non-empty-string> */
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
     * @return Set<non-empty-string>
     */
    private static function address(): Set
    {
        return self::string(64, '-', '.', '_');
    }

    /**
     * @return Set<non-empty-string>
     */
    private static function domain(): Set
    {
        return self::string(63, '-', '.');
    }

    /**
     * @param non-empty-string $extra
     *
     * @return Set<non-empty-string>
     */
    private static function string(int $maxLength, string ...$extra): Set
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion Due to array not being a list
         * @psalm-suppress InvalidArgument Same problem as above
         */
        return Set\Either::any(
            // either only with simple characters
            Set\Sequence::of(
                self::letter(),
                Set\Integers::between(1, $maxLength),
            )->map(static fn(array $chars): string => \implode('', $chars)),
            // or with some extra ones in the middle
            Set\Composite::immutable(
                static fn(...$parts): string => \implode('', $parts),
                self::letter(),
                Set\Sequence::of(
                    self::letter(...$extra),
                    Set\Integers::between(1, $maxLength - 2),
                )->map(static fn(array $chars): string => \implode('', $chars)),
                self::letter(),
            )->filter(static function(string $string): bool {
                return !\preg_match('~\.\.~', $string);
            }),
        );
    }

    /**
     * @return Set<non-empty-string>
     */
    private static function tld(): Set
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion Due to array not being a list
         * @var Set<non-empty-string>
         */
        return Set\Sequence::of(
            Set\Elements::of(...\range('a', 'z'), ...\range('A', 'Z')),
            Set\Integers::between(1, 63),
        )->map(static fn(array $chars): string => \implode('', $chars));
    }

    /**
     * @param non-empty-string $extra
     *
     * @return Set<non-empty-string>
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
