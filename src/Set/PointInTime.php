<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\TimeContinuum\PointInTime\Earth\PointInTime as Model;

final class PointInTime
{
    public static function of(string $name): Set
    {
        return Composite::of(
            $name,
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
            Integers::of('year', 0, 9999)->take(10), // the numbers taken is arbitrary in order to reduce the numbers of combinations generated
            Integers::of('month', 1, 12)->take(3),
            Integers::of('day', 1, 31)->take(3),
            Integers::of('hour', 0, 23)->take(3),
            Integers::of('minute', 0, 59)->take(3),
            Integers::of('second', 0, 59)->take(3),
            Elements::of('offsetDirection', '-', '+'),
            Integers::of('hourOffset', 0, 12)->take(3),
            Elements::of('minuteOffset', '00', '15', '30', '45')->take(1)
        )->take(100);
    }
}
