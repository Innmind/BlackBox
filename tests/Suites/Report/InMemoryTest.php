<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Suites;

use Innmind\BlackBox\{
    Suites\Report\InMemory,
    Suites\Report,
    Test,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\{
    Map,
    StreamInterface,
};
use PHPUnit\Framework\TestCase;

class InMemoryTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Report::class, new InMemory);
    }

    public function testAddSuccessfulReport()
    {
        $report = new InMemory;

        $this->assertInstanceOf(StreamInterface::class, $report->failures());
        $this->assertSame(Test\Report::class, (string) $report->failures()->type());
        $this->assertCount(0, $report->failures());

        $report2 = $report->add(new Test\Report(new Test\Name('foo')));

        $this->assertSame($report2, $report);
        $this->assertCount(0, $report->failures());
    }

    public function testAddFailureReport()
    {
        $report = new InMemory;

        $this->assertInstanceOf(StreamInterface::class, $report->failures());
        $this->assertSame(Test\Report::class, (string) $report->failures()->type());
        $this->assertCount(0, $report->failures());

        $report2 = $report->add(
            $testReport = (new Test\Report(new Test\Name('foo')))
                ->add(
                    new Scenario(new Map('string', 'mixed')),
                    new Result(null),
                    (new ScenarioReport)->fail('foo')
                )
        );

        $this->assertNotSame($report2, $report);
        $this->assertCount(0, $report->failures());
        $this->assertCount(1, $report2->failures());
        $this->assertSame($testReport, $report2->failures()->first());
    }
}
