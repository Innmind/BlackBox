<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Set,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\{
    Map,
    Set as ISet,
};
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Set('int'));
    }

    public function testInvokation()
    {
        $assert = new Set('int');

        $report = $assert(
            new ScenarioReport,
            new Result(ISet::of('int')),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            new ScenarioReport,
            new Result('foo'),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a set', $report->failure());

        $report = $assert(
            new ScenarioReport,
            new Result(ISet::of('mixed')),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a set of type <int>, got <mixed>', $report->failure());
    }
}
