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
            Strings::any()->filter(static function(string $string): bool {
                return (bool) \preg_match('~^[a-zA-Z0-9][a-zA-Z0-9+\-\._]+[a-zA-Z0-9]$~', $string) &&
                    !\preg_match('~\.\.~', $string);
            }),
            Strings::any()->filter(static function(string $string): bool {
                return (bool) \preg_match('~^[a-zA-Z0-9][a-zA-Z0-9\-\.]+[a-zA-Z0-9]$~', $string) &&
                    !\preg_match('~\.\.~', $string);
            }),
            Strings::any()->filter(static function(string $string): bool {
                return (bool) \preg_match('~^[a-zA-Z]+$~', $string);
            }),
        )
            ->take(100)
            ->filter(static function(string $email): bool {
                return (bool) !\preg_match('~(\-.|\.\-)~', $email);
            });
    }
}
