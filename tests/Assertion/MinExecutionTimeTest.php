<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\MinExecutionTime,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriod;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class MinExecutionTimeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Assertion::class,
            new MinExecutionTime(
                new ElapsedPeriod(0)
            )
        );
    }

    public function testFailWhenTookTooLong()
    {
        $assert = new MinExecutionTime(new ElapsedPeriod(10));

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(null, new ElapsedPeriod(9)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Test executed in 9 ms', (string) $report->failure()->message());
    }

    public function testSuccessWhenLessThanThreshold()
    {
        $assert = new MinExecutionTime(new ElapsedPeriod(10));

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(null, new ElapsedPeriod(11)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());
    }
}
