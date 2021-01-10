<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Uuid
{
    /**
     * @return Set<string>
     */
    public static function any(): Set
    {
        $chars = Set\Elements::of(...\range('a', 'f'), ...\range(0, 9));
        $part = static fn(int $length): Set => Set\Decorate::immutable(
            static fn(array $chars): string => \implode('', $chars),
            Sequence::of(
                $chars,
                Integers::between($length, $length),
            ),
        );

        return Set\Composite::immutable(
            static fn(string ...$parts): string => \implode('-', $parts),
            $part(8),
            $part(4),
            $part(4),
            $part(4),
            $part(12),
        )->take(100);
    }
}
