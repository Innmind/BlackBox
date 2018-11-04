<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriodInterface;

final class MaxExecutionTime implements Assertion
{
    private $threshold;

    public function __construct(ElapsedPeriodInterface $threshold)
    {
        $this->threshold = $threshold;
    }

    public function __invoke(
        OperatingSystem $os,
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if ($result->executionTime()->longerThan($this->threshold)) {
            return $report->fail("Test executed in {$result->executionTime()->milliseconds()} ms");
        }

        return $report->success();
    }
}
