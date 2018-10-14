<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\NotSame,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class NotSameTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new NotSame(42));
    }

    public function testInvokation()
    {
        $assert = new NotSame(1);

        $report = $assert(
            new ScenarioReport,
            new Result(2),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new NotSame(1);

        $report = $assert(
            new ScenarioReport,
            new Result(1),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Same', (string) $report->failure()->message());
    }
}
