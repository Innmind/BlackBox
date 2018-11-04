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

final class Instance implements Assertion
{
    private $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function __invoke(
        OperatingSystem $os,
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (!$result->value() instanceof $this->class) {
            return $report->fail("Not an instance of $this->class");
        }

        return $report->success();
    }
}
