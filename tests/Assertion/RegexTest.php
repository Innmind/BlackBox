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
            new ScenarioReport,
            new Result('foobar'),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            new ScenarioReport,
            new Result('bar'),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not matches ~foo~', $report->failure());
    }

    public function testInvokationOnStr()
    {
        $assert = new Regex('~foo~');

        $report = $assert(
            new ScenarioReport,
            new Result(Str::of('foobar')),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            new ScenarioReport,
            new Result(Str::of('bar')),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not matches ~foo~', $report->failure());
    }

    public function testFailWhenNotRegexable()
    {
        $assert = new Regex('~foo~');

        $report = $assert(
            new ScenarioReport,
            new Result(42),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not regexable', $report->failure());
    }
}
