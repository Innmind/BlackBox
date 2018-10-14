<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\SetInterface;

final class Set implements Assertion
{
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (!$result->value() instanceof SetInterface) {
            return $report->fail('Not a set');
        }

        if ((string) $result->value()->type() !== $this->type) {
            return $report->fail("Not a set of type <{$this->type}>, got <{$result->value()->type()}>");
        }

        return $report->success();
    }
}
