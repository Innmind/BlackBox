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
            Strings::matching('[a-zA-Z0-9][a-zA-Z0-9+\-\._]{1,62}[a-zA-Z0-9]')->filter(static function(string $string): bool {
                return !\preg_match('~\.\.~', $string);
            }),
            Strings::matching('[a-zA-Z0-9][a-zA-Z0-9\-\.]{1,62}[a-zA-Z0-9]')->filter(static function(string $string): bool {
                return !\preg_match('~\.\.~', $string);
            }),
            Strings::matching('[a-zA-Z]{1,63}'),
        )
            ->take(100)
            ->filter(static function(string $email): bool {
                return (bool) !\preg_match('~(\-.|\.\-)~', $email);
            });
    }
}
