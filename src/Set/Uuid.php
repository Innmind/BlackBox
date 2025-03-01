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
        $chars = Set::elements(...\range('a', 'f'), ...\range(0, 9));
        /** @psalm-suppress ArgumentTypeCoercion */
        $part = static fn(int $length): Set => Sequence::of($chars)
            ->between($length, $length)
            ->map(static fn(array $chars): string => \implode('', $chars));

        /** @var Set<non-empty-string> */
        return Set::of(Composite::immutable(
            static fn(string ...$parts): string => \implode('-', $parts),
            $part(8),
            $part(4),
            $part(4),
            $part(4),
            $part(12),
        ))->take(100);
    }
}
