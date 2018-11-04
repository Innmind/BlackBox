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
use Innmind\Immutable\Str;

final class Regex implements Assertion
{
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    public function __invoke(
        OperatingSystem $os,
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        switch (true) {
            case $result->value() instanceof Str:
                if ($result->value()->matches($this->regex)) {
                    return $report->success();
                }
                break;

            case \is_string($result->value()):
                if ((bool) \preg_match($this->regex, $result->value())) {
                    return $report->success();
                }
                break;

            default:
                return $report->fail('Not regexable');
        }

        return $report->fail("Not matches {$this->regex}");
    }
}
