<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Count,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Count(42));
    }

    /**
     * @dataProvider values
     */
    public function testInvokation($collection, $good, $bad)
    {
        $assert = new Count($good);

        $report = $assert(
            new ScenarioReport,
            new Result($collection),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new Count($bad);

        $report = $assert(
            new ScenarioReport,
            new Result($collection),
            new Scenario(new Map('string', 'mixed'))
        );

        $got = \count($collection);
        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame("Expected count of $bad, counted $got", (string) $report->failure()->message());
    }

    public function testFailWhenNotCountable()
    {
        $assert = new Count(42);

        $report = $assert(
            new ScenarioReport,
            new Result('foo'),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame('Not countable', (string) $report->failure()->message());
    }

    public function values(): array
    {
        return [
            [[1, 2], 2, 3],
            [
                new class implements \Countable {
                    public function count()
                    {
                        return 42;
                    }
                },
                42,
                24,
            ],
        ];
    }
}
