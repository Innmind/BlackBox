<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\That,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ThatTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new That(function(){}));
    }

    public function testInvokation()
    {
        $result = new Result(1);
        $scenario = new Scenario(new Map('string', 'mixed'));
        $assert = new That(function($a, $b) use ($result, $scenario) {
            return $a === $result->value() && $b === $scenario;
        });

        $report = $assert(
            new ScenarioReport,
            $result,
            $scenario
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new That(function() {
            return false;
        });

        $report = $assert(
            new ScenarioReport,
            new Result(2),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Does not match predicate', $report->failure());
    }
}
