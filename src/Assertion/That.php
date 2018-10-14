<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};

final class That implements Assertion
{
    private $predicate;

    public function __construct(callable $predicate)
    {
        $this->predicate = \Closure::bind($predicate, null);
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (($this->predicate)($result->value(), $scenario) === true) {
            return $report->success();
        }

        return $report->fail('Does not match predicate');
    }
}
