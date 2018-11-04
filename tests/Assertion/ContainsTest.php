<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Contains,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriodInterface;
use Innmind\Immutable\{
    Map,
    Set,
    Stream,
    Sequence,
    Str,
};
use PHPUnit\Framework\TestCase;

class ContainsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Contains('foo'));
    }

    /**
     * @dataProvider values
     */
    public function testInvokation($result, $good, $bad)
    {
        $assert = new Contains($good);

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result($result, $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new Contains($bad);

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result($result, $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Does not contain expected value', (string) $report->failure()->message());
    }

    public function testFailWhenResultNotACollection()
    {
        $assert = new Contains('foo');

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new class {}, $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a collection', (string) $report->failure()->message());
    }

    public function values(): array
    {
        return [
            [
                (new Map('int', 'int'))
                    ->put(42, 24),
                42,
                24,
            ],
            [Set::of('int', 42), 42, 24],
            [Stream::of('int', 42), 42, 24],
            [Sequence::of('int', 42), 42, 24],
            [Str::of('foo'), 'fo', 'bar'],
            ['foo', 'fo', 'bar'],
            [[42], 42, 0],
        ];
    }
}
