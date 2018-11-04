<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Then,
    Then\ScenarioReport,
    Given\Scenario,
    When\Result,
    Assertion,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ThenTest extends TestCase
{
    public function testInvokation()
    {
        $assert = new Then(
            $assertion = $this->createMock(Assertion::class),
            $assertion2 = $this->createMock(Assertion::class),
            $assertion3 = $this->createMock(Assertion::class)
        );
        $os = $this->createMock(OperatingSystem::class);
        $result = new Result(null);
        $scenario = new Scenario(new Map('string', 'mixed'));
        $assertion
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $os,
                $this->callback(static function($report): bool {
                    return $report instanceof ScenarioReport;
                }),
                $result,
                $scenario
            )
            ->will($this->returnCallback(static function($os, $report) {
                return $report->success();
            }));
        $assertion2
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $os,
                $this->callback(static function($report): bool {
                    return $report instanceof ScenarioReport;
                }),
                $result,
                $scenario
            )
            ->will($this->returnCallback(static function($os, $report) {
                return $report->fail('something');
            }));
        $assertion3
            ->expects($this->never())
            ->method('__invoke');

        $report = $assert($os, $result, $scenario);

        $this->assertInstanceOf(ScenarioReport::class, $report);
        $this->assertTrue($report->failed());
        $this->assertSame('something', (string) $report->failure()->message());
        $this->assertSame(2, $report->assertions());
    }
}
