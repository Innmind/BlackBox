<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

enum Tag
{
    case windows;
    case linux;
    case macOS;
    case positive;
    case negative;
    case wip;

    public static function of(string $name): ?self
    {
        return match ($name) {
            'windows' => self::windows,
            'linux' => self::linux,
            'macOS' => self::macOS,
            'positive' => self::positive,
            'negative' => self::negative,
            'wip' => self::wip,
            default => null,
        };
    }
}
