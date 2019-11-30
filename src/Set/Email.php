<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Email
{
    /**
     * @return Set<Model>
     */
    public static function any(): Set
    {
        return Composite::of(
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
            })
        )
            ->take(100)
            ->filter(static function($email): bool {
                return (bool) !\preg_match('~(\-.|\.\-)~', $email);
            });
    }
}