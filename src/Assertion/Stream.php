<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\StreamInterface;

final class Stream implements Assertion
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
        if (!$result->value() instanceof StreamInterface) {
            return $report->fail('Not a stream');
        }

        if ((string) $result->value()->type() !== $this->type) {
            return $report->fail("Not a stream of type <{$this->type}>, got <{$result->value()->type()}>");
        }

        return $report->success();
    }
}
