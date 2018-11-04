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

final class MinExecutionTime implements Assertion
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
        if ($this->threshold->longerThan($result->executionTime())) {
            return $report->fail("Test executed in {$result->executionTime()->milliseconds()} ms");
        }

        return $report->success();
    }
}
