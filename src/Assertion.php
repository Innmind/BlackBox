<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;

interface Assertion
{
    public function __invoke(
        OperatingSystem $os,
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport;
}
