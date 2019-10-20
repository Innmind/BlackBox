<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Url\Url as Model;

final class Url
{
    /**
     * @return Set<Model>
     */
    public static function any(): Set
    {
        return Composite::of(
            static function(
                string $scheme,
                string $user,
                string $password,
                string $host,
                int $port,
                string $path,
                string $query,
                string $fragment
            ): Model {
                return Model::fromString("$scheme://$user:$password@$host:$port/$path?$query#$fragment");
            },
            Elements::of('http', 'https', 'ftp', 'ssh'),
            Strings::any()->filter(static function(string $user): bool {
                return (bool) \preg_match('/^[\pL\pN-]+$/', $user);
            }),
            Strings::any()->filter(static function(string $user): bool {
                return (bool) \preg_match('/^[\pL\pN-]+$/', $user);
            }),
            Elements::of('example.com', '127.0.0.1'),
            Integers::between(0, 9999),
            Strings::any(),
            Strings::any(),
            Strings::any()
        )->take(100);
    }

    /**
     * @deprecated
     * @see self::any()
     */
    public static function of(): Set
    {
        return self::any();
    }
}
