<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\PointInTime,
    Set,
};
use Innmind\TimeContinuum\PointInTime\Earth\PointInTime as Model;
use PHPUnit\Framework\TestCase;

class PointInTimeTest extends TestCase
{
    public function testOf()
    {
        $pointsInTime = PointInTime::of();
        $values = $pointsInTime->reduce(
            [],
            static function(array $values, Model $point): array {
                $values[] = $point;

                return $values;
            }
        );

        $this->assertInstanceOf(Set::class, $pointsInTime);
        $this->assertCount(100, $values);
    }
}
