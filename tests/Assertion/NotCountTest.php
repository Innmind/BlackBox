<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\NotCount,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class NotCountTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new NotCount(42));
    }

    /**
     * @dataProvider values
     */
    public function testInvokation($collection, $good, $bad)
    {
        $assert = new NotCount($good);

        $report = $assert(
            new ScenarioReport,
            new Result($collection),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new NotCount($bad);

        $report = $assert(
            new ScenarioReport,
            new Result($collection),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame("Unexpected count of $bad", (string) $report->failure()->message());
    }

    public function testFailWhenNotCountable()
    {
        $assert = new NotCount(42);

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
            [[1, 2], 3, 2],
            [
                new class implements \Countable {
                    public function count()
                    {
                        return 42;
                    }
                },
                24,
                42,
            ],
        ];
    }
}