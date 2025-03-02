<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Uuid
{
    /**
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        /** @var Set<scalar> */
        $chars = Set::of(...\range('a', 'f'), ...\range(0, 9));
        /** @psalm-suppress ArgumentTypeCoercion */
        $part = static fn(int $length): Set => Set::sequence($chars)
            ->between($length, $length)
            ->toSet()
            ->map(static fn(array $chars): string => \implode('', $chars));

        /** @var Set<non-empty-string> */
        return Set::composite(
            static fn(string ...$parts): string => \implode('-', $parts),
            $part(8),
            $part(4),
            $part(4),
            $part(4),
            $part(12),
        )
            ->immutable()
            ->toSet()
            ->take(100);
    }
}
