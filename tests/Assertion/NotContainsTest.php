<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\NotContains,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\{
    Map,
    Set,
    Stream,
    Sequence,
    Str,
};
use PHPUnit\Framework\TestCase;

class NotContainsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new NotContains('foo'));
    }

    /**
     * @dataProvider values
     */
    public function testInvokation($result, $good, $bad)
    {
        $assert = new NotContains($good);

        $report = $assert(
            new ScenarioReport,
            new Result($result),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new NotContains($bad);

        $report = $assert(
            new ScenarioReport,
            new Result($result),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Contains expected value', $report->failure());
    }

    public function testFailWhenResultNotACollection()
    {
        $assert = new NotContains('foo');

        $report = $assert(
            new ScenarioReport,
            new Result(new class {}),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a collection', $report->failure());
    }

    public function values(): array
    {
        return [
            [
                (new Map('int', 'int'))
                    ->put(42, 24),
                24,
                42,
            ],
            [Set::of('int', 42), 24, 42],
            [Stream::of('int', 42), 24, 42],
            [Sequence::of('int', 42), 24, 42],
            [Str::of('foo'), 'bar', 'fo'],
            ['foo', 'bar', 'fo'],
            [[42], 0, 42],
        ];
    }
}
