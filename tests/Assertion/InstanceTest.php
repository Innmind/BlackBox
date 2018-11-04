<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Instance,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class InstanceTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Instance('stdClass'));
    }

    public function testInvokation()
    {
        $assert = new Instance('stdClass');

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \stdClass),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result('foo'),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not an instance of stdClass', (string) $report->failure()->message());
    }
}
