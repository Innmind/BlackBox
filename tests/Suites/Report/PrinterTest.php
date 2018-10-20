<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Suites\Report;

use Innmind\BlackBox\{
    Suites\Report\Printer,
    Suites\Report,
    Test,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Stream\Writable;
use Innmind\Immutable\{
    Stream,
    Str,
    Map,
};
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Report::class,
            new Printer(
                $this->createMock(Writable::class),
                $this->createMock(Report::class)
            )
        );
    }

    public function testAddSuccessfulReport()
    {
        $report = new Printer(
            $stream = $this->createMock(Writable::class),
            $inner = $this->createMock(Report::class)
        );
        $testReport = new Test\Report(new Test\Name('foo'));
        $stream
            ->expects($this->once())
            ->method('write')
            ->with(Str::of('.'));
        $inner
            ->expects($this->any())
            ->method('failures')
            ->willReturn($failures = Stream::of(Test\Report::class));
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($testReport)
            ->willReturn($inner2 = $this->createMock(Report::class));
        $inner2
            ->expects($this->any())
            ->method('failures')
            ->willReturn($failures2 = Stream::of(Test\Report::class));

        $this->assertSame($failures, $report->failures());

        $report2 = $report->add($testReport);

        $this->assertSame($report2, $report);
        $this->assertNotSame($failures, $report2->failures());
        $this->assertSame($failures2, $report2->failures());
    }

    public function testAddFailureReport()
    {
        $report = new Printer(
            $stream = $this->createMock(Writable::class),
            $inner = $this->createMock(Report::class)
        );
        $testReport = (new Test\Report(new Test\Name('foo')))
            ->add(
                new Scenario(new Map('string', 'mixed')),
                new Result(null),
                (new ScenarioReport)->fail('foo')
            );
        $stream
            ->expects($this->once())
            ->method('write')
            ->with(Str::of('F'));
        $inner
            ->expects($this->any())
            ->method('failures')
            ->willReturn($failures = Stream::of(Test\Report::class));
        $inner
            ->expects($this->once())
            ->method('add')
            ->with($testReport)
            ->willReturn($inner2 = $this->createMock(Report::class));
        $inner2
            ->expects($this->any())
            ->method('failures')
            ->willReturn($failures2 = Stream::of(Test\Report::class));

        $this->assertSame($failures, $report->failures());

        $report2 = $report->add($testReport);

        $this->assertSame($report2, $report);
        $this->assertNotSame($failures, $report2->failures());
        $this->assertSame($failures2, $report2->failures());
    }
}
