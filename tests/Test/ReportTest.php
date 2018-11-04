<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Test;

use Innmind\BlackBox\{
    Test\Report,
    Test\Name,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
    Exception\LogicException,
};
use Innmind\TimeContinuum\ElapsedPeriodInterface;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    public function testAddSuccessReport()
    {
        $report = new Report(new Name('foo'));

        $this->assertFalse($report->failed());
        $this->assertSame(0, $report->assertions());
        $this->assertSame(
            $report,
            $report->add(
                new Scenario(new Map('string', 'mixed')),
                new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                (new ScenarioReport)
                    ->success()
                    ->success()
            )
        );
        $this->assertFalse($report->failed());
        $this->assertSame(2, $report->assertions());
    }

    public function testAddFailedReport()
    {
        $report = new Report(new Name('foo'));

        $this->assertFalse($report->failed());
        $this->assertSame(0, $report->assertions());
        $this->assertSame(
            $report,
            $report->add(
                $scenario = new Scenario(new Map('string', 'mixed')),
                $result = new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                $scenarioReport = (new ScenarioReport)
                    ->success()
                    ->fail('foo')
            )
        );
        $this->assertTrue($report->failed());
        $this->assertSame(2, $report->assertions());
        $this->assertSame($scenario, $report->failedScenario());
        $this->assertSame($result, $report->failedResult());
        $this->assertSame($scenarioReport->failure(), $report->failure());
    }

    public function testThrowWhenAddingSuccessReportAfterAFailure()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No report must be added after a failure');

        (new Report(new Name('foo')))
            ->add(
                new Scenario(new Map('string', 'mixed')),
                new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                (new ScenarioReport)->fail('')
            )
            ->add(
                new Scenario(new Map('string', 'mixed')),
                new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                new ScenarioReport
            );
    }

    public function testThrowWhenAddingFailedReportAfterAFailure()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No report must be added after a failure');

        (new Report(new Name('foo')))
            ->add(
                new Scenario(new Map('string', 'mixed')),
                new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                (new ScenarioReport)->fail('')
            )
            ->add(
                new Scenario(new Map('string', 'mixed')),
                new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                (new ScenarioReport)->fail('')
            );
    }
}
