<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\TimeContinuum\PointInTime\Earth\PointInTime as Model;

final class PointInTime
{
    /**
     * @return Set<Model>
     */
    public static function of(): Set
    {
        return Composite::of(
            static function(
                int $year,
                int $month,
                int $day,
                int $hour,
                int $minute,
                int $second,
                string $offsetDirection,
                int $hourOffset,
                string $minuteOffset
            ): Model {
                return new Model("$year-$month-{$day}T$hour:$minute:$second$offsetDirection$hourOffset:$minuteOffset");
            },
            Integers::of(0, 9999),
            Integers::of(1, 12),
            Integers::of(1, 31),
            Integers::of(0, 23),
            Integers::of(0, 59),
            Integers::of(0, 59),
            Elements::of('-', '+'),
            Integers::of(0, 12),
            Elements::of('00', '15', '30', '45')
        )->take(100);
    }
}
