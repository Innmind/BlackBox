<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Exception,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Assertion::class,
            new Exception('foo')
        );
    }

    public function testValidateExceptionClass()
    {
        $assert = new Exception(\RuntimeException::class);

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \RuntimeException),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());
    }

    public function testValidateExceptionMessage()
    {
        $assert = new Exception(
            \RuntimeException::class,
            'bar'
        );

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \RuntimeException('foobarbaz')),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());
    }

    public function testValidateExceptionCode()
    {
        $assert = new Exception(
            \RuntimeException::class,
            null,
            42
        );

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \RuntimeException('', 42)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());
    }

    public function testFailWhenNotAnException()
    {
        $assert = new Exception(\RuntimeException::class);

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(42),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not an exception', (string) $report->failure()->message());
    }

    public function testFailWhenDifferentException()
    {
        $assert = new Exception(\RuntimeException::class);

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \LogicException),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not exception RuntimeException', (string) $report->failure()->message());
    }

    public function testFailWhenExceptionMessageDoesntContainExpectedOne()
    {
        $assert = new Exception(
            \RuntimeException::class,
            'foo'
        );

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \RuntimeException),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame("Exception message doesn't contain \"foo\"", (string) $report->failure()->message());
    }

    public function testFailWhenExceptionCodeDifferentThanExpectedOne()
    {
        $assert = new Exception(
            \RuntimeException::class,
            null,
            42
        );

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(new \RuntimeException),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Exception code is different than 42', (string) $report->failure()->message());
    }
}
