<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\PointInTime,
    Set,
};
use Innmind\TimeContinuum\{
    PointInTimeInterface,
    Format\ISO8601,
};
use PHPUnit\Framework\TestCase;

class PointInTimeTest extends TestCase
{
    public function testOf()
    {
        $pointsInTime = PointInTime::of();

        $this->assertInstanceOf(Set::class, $pointsInTime);
        $this->assertCount(100, \iterator_to_array($pointsInTime->values()));

        foreach ($pointsInTime->values() as $pointInTime) {
            $this->assertInstanceOf(PointInTimeInterface::class, $pointInTime);
        }
    }

    public function testAfter()
    {
        $points = PointInTime::after('1970-01-01T12:13:14+02:00');

        $this->assertInstanceOf(Set::class, $points);
        $this->assertCount(100, \iterator_to_array($points->values()));

        foreach ($points->values() as $point) {
            $this->assertGreaterThanOrEqual(
                '1970-01-01T12:13:14+02:00',
                $point->format(new ISO8601)
            );
        }
    }

    public function testBefore()
    {
        $points = PointInTime::before('1970-01-01T12:13:14+02:00');

        $this->assertInstanceOf(Set::class, $points);
        $this->assertCount(100, \iterator_to_array($points->values()));

        foreach ($points->values() as $point) {
            $this->assertLessThanOrEqual(
                '1970-01-01T12:13:14+02:00',
                $point->format(new ISO8601)
            );
        }
    }
}
