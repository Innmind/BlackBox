<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Map,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Map as IMap;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Map('int', 'int'));
    }

    public function testInvokation()
    {
        $assert = new Map('int', 'string');

        $report = $assert(
            new ScenarioReport,
            new Result(new IMap('int', 'string')),
            new Scenario(new IMap('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            new ScenarioReport,
            new Result('foo'),
            new Scenario(new IMap('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a map', $report->failure());

        $report = $assert(
            new ScenarioReport,
            new Result(new IMap('mixed', 'string')),
            new Scenario(new IMap('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a map of type <int, string>, got <mixed, string>', $report->failure());
    }
}
