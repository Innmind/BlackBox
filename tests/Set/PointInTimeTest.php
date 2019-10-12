<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\PointInTime,
    Set,
};
use Innmind\TimeContinuum\PointInTimeInterface;
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
}
