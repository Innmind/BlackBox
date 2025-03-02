<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Email
{
    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        return Set::compose(
            static function(string $address, string $domain, string $tld): string {
                return "$address@$domain.$tld";
            },
            self::address(),
            self::domain(),
            self::tld(),
        )
            ->immutable()
            ->take(100)
            ->filter(static function(string $email): bool {
                return !\preg_match('~(\-.|\.\-)~', $email);
            });
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    private static function address(): Set
    {
        return self::string(64, '-', '.', '_');
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    private static function domain(): Set
    {
        return self::string(63, '-', '.');
    }

    /**
     * @psalm-pure
     *
     * @param int<3, max> $maxLength
     * @param non-empty-string $extra
     *
     * @return Set<non-empty-string>
     */
    private static function string(int $maxLength, string ...$extra): Set
    {
        /** @var Set<non-empty-string> */
        return Set::either(
            // either only with simple characters
            Set::sequence(self::letter())
                ->between(1, $maxLength)
                ->map(static fn(array $chars): string => \implode('', $chars)),
            // or with some extra ones in the middle
            Set::compose(
                static fn(string ...$parts): string => \implode('', $parts),
                self::letter(),
                Set::sequence(self::letter(...$extra))
                    ->between(1, $maxLength - 2)
                    ->map(static fn(array $chars): string => \implode('', $chars)),
                self::letter(),
            )
                ->immutable()
                ->filter(static function(string $string): bool {
                    return !\preg_match('~\.\.~', $string);
                }),
        );
    }

    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    private static function tld(): Set
    {
        /**
         * @var Set<non-empty-string>
         */
        return Set::sequence(Set::of(...\range('a', 'z'), ...\range('A', 'Z')))
            ->between(1, 63)
            ->map(static fn(array $chars): string => \implode('', $chars));
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $extra
     *
     * @return Set<non-empty-string>
     */
    private static function letter(string ...$extra): Set
    {
        /** @var Set<non-empty-string> */
        return Set::of(
            ...\range('a', 'z'),
            ...\range('A', 'Z'),
            ...\array_map(
                static fn($i) => (string) $i,
                \range(0, 9),
            ),
            ...$extra,
        );
    }
}
