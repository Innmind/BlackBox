<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
    Exception\LogicException,
};

final class Primitive implements Assertion
{
    private $function;
    private $type;

    public function __construct(string $type)
    {
        $function = "is_$type";

        if (!\function_exists($function)) {
            throw new LogicException("$type is not a primitive");
        }

        $this->function = $function;
        $this->type = $type;
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (($this->function)($result->value())) {
            return $report->success();
        }

        return $report->fail("Not a {$this->type}");
    }
}
