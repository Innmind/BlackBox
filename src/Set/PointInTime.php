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
    public static function any(): Set
    {
        return self::yearRange(0, 9999);
    }

    /**
     * @param string $point String representation of a date
     * @return Set<Model>
     */
    public static function after(string $point): Set
    {
        $lower = new Model($point);

        return self::yearRange($lower->year()->toInt(), 9999)->filter(static function($point) use ($lower): bool {
            return $point->aheadOf($lower);
        });
    }

    /**
     * @param string $point String representation of a date
     * @return Set<Model>
     */
    public static function before(string $point): Set
    {
        $upper = new Model($point);

        return self::yearRange(0, $upper->year()->toInt())->filter(static function($point) use ($upper): bool {
            return $upper->aheadOf($point);
        });
    }

    /**
     * @deprecated
     * @see self::any()
     */
    public static function of(): Set
    {
        return self::any();
    }

    /**
     * @return Set<Model>
     */
    private static function yearRange(int $lowerBound, int $upperBound): Set
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
            Integers::between($lowerBound, $upperBound),
            Integers::between(1, 12),
            Integers::between(1, 31),
            Integers::between(0, 23),
            Integers::between(0, 59),
            Integers::between(0, 59),
            Elements::of('-', '+'),
            Integers::between(0, 12),
            Elements::of('00', '15', '30', '45')
        )->take(100);
    }
}
