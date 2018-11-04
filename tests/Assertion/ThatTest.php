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
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriodInterface;
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
        $result = new Result(1, $this->createMock(ElapsedPeriodInterface::class));
        $scenario = new Scenario(new Map('string', 'mixed'));
        $assert = new That(function($a, $b) use ($result, $scenario) {
            return $a === $result->value() && $b === $scenario;
        });

        $report = $assert(
            $this->createMock(OperatingSystem::class),
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
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(2, $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Does not match predicate', (string) $report->failure()->message());
    }
}
