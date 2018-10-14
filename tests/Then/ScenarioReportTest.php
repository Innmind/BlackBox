<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Then;

use Innmind\BlackBox\{
    Then\ScenarioReport,
    Exception\LogicException,
};
use PHPUnit\Framework\TestCase;

class ScenarioReportTest extends TestCase
{
    public function testSuccess()
    {
        $report = new ScenarioReport;

        $this->assertFalse($report->failed());
        $this->assertSame(0, $report->assertions());
        $report2 = $report->success();
        $this->assertInstanceOf(ScenarioReport::class, $report2);
        $this->assertNotSame($report, $report2);
        $this->assertFalse($report->failed());
        $this->assertFalse($report2->failed());
        $this->assertSame(0, $report->assertions());
        $this->assertSame(1, $report2->assertions());
    }

    public function testFailure()
    {
        $report = new ScenarioReport;

        $this->assertFalse($report->failed());
        $report2 = $report->fail('something went wrong');
        $this->assertInstanceOf(ScenarioReport::class, $report2);
        $this->assertNotSame($report, $report2);
        $this->assertFalse($report->failed());
        $this->assertTrue($report2->failed());
        $this->assertSame(0, $report->assertions());
        $this->assertSame(1, $report2->assertions());
        $this->assertSame('something went wrong', (string) $report2->failure()->message());

        $this->expectException(\TypeError::class);

        $report->failure();
    }

    public function testOnlyOneFailureCanBeReported()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only one failure can be reported for a scenario');

        (new ScenarioReport)
            ->fail('foo')
            ->fail('bar');
    }

    public function testSuccessCantBeReportedAfterAFailure()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No success should be reported after a failure');

        (new ScenarioReport)
            ->fail('foo')
            ->success();
    }
}
