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

final class Same implements Assertion
{
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke(
        OperatingSystem $os,
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if ($result->value() === $this->value) {
            return $report->success();
        }

        return $report->fail('Not same');
    }
}
