<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\MaxExecutionTime,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriod;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class MaxExecutionTimeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Assertion::class,
            new MaxExecutionTime(
                new ElapsedPeriod(0)
            )
        );
    }

    public function testFailWhenTookTooLong()
    {
        $assert = new MaxExecutionTime(new ElapsedPeriod(10));

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(null, new ElapsedPeriod(11)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Test executed in 11 ms', (string) $report->failure()->message());
    }

    public function testSuccessWhenLessThanThreshold()
    {
        $assert = new MaxExecutionTime(new ElapsedPeriod(10));

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(null, new ElapsedPeriod(9)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());
    }
}
