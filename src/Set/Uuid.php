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
        $bits = [];

        foreach (\range(0, 31) as $_) {
            $bits[] = self::bit();
        }

        /** @var Set<string> */
        return Composite::of(
            static function(string ...$bits): string {
                $chunks = \array_chunk($bits, 4);
                $chunks = \array_map(
                    static function(array $bits): string {
                        return implode('', $bits);
                    },
                    $chunks
                );

                return "{$chunks[0]}{$chunks[1]}-{$chunks[2]}-{$chunks[3]}-{$chunks[4]}-{$chunks[5]}{$chunks[6]}{$chunks[7]}";
            },
            ...$bits,
        )->take(100);
    }

    private static function bit(): Set
    {
        return Chars::any()->filter(static function(string $bit): bool {
            return (bool) \preg_match('~^[a-f0-9]$~', $bit);
        });
    }
}
