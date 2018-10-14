<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};

final class Count implements Assertion
{
    private $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (!\is_array($result->value()) && !$result->value() instanceof \Countable) {
            return $report->fail('Not countable');
        }

        $count = \count($result->value());

        if ($count === $this->count) {
            return $report->success();
        }

        return $report->fail("Expected count of {$this->count}, counted $count");
    }
}
