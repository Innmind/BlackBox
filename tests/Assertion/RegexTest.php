<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Regex,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriodInterface;
use Innmind\Immutable\{
    Map,
    Str,
};
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Regex('~foo~'));
    }

    public function testInvokationOnString()
    {
        $assert = new Regex('~foo~');

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result('foobar', $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result('bar', $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not matches ~foo~', (string) $report->failure()->message());
    }

    public function testInvokationOnStr()
    {
        $assert = new Regex('~foo~');

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(Str::of('foobar'), $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(Str::of('bar'), $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not matches ~foo~', (string) $report->failure()->message());
    }

    public function testFailWhenNotRegexable()
    {
        $assert = new Regex('~foo~');

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(42, $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not regexable', (string) $report->failure()->message());
    }
}
