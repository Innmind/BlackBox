<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    When,
    When\Result,
    Given\Scenario,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\{
    TimeContinuumInterface,
    PointInTimeInterface,
    ElapsedPeriodInterface,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class WhenTest extends TestCase
{
    public function testInvokation()
    {
        $expected = new Scenario(new Map('string', 'mixed'));
        $os = $this->createMock(OperatingSystem::class);
        $os
            ->expects($this->any())
            ->method('clock')
            ->willReturn($clock = $this->createMock(TimeContinuumInterface::class));
        $clock
            ->expects($this->at(0))
            ->method('now')
            ->willReturn($start = $this->createMock(PointInTimeInterface::class));
        $clock
            ->expects($this->at(1))
            ->method('now')
            ->willReturn($end = $this->createMock(PointInTimeInterface::class));
        $end
            ->expects($this->once())
            ->method('elapsedSince')
            ->with($start)
            ->willReturn($executionTime = $this->createMock(ElapsedPeriodInterface::class));

        $when = new When(function($scenario) use ($expected) {
            if ($scenario !== $expected) {
                throw new \Exception;
            }

            return 42;
        });

        $result = $when($os, $expected);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(42, $result->value());
        $this->assertSame($executionTime, $result->executionTime());
    }

    public function testThrowWhenTryingToAccessThisInsideTheCallable()
    {
        $when = new When(function() {
            $this->assertSame(42, 42);
        });
        $os = $this->createMock(OperatingSystem::class);

        $result = $when($os, new Scenario(new Map('string', 'mixed')));

        $this->assertInstanceOf(
            \Error::class,
            $result->value()
        );
        $this->assertSame(
            'Using $this when not in object context',
            $result->value()->getMessage()
        );
    }
}
