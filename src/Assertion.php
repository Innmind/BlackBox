<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};

interface Assertion
{
    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport;
}
