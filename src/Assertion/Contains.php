<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    StreamInterface,
    SequenceInterface,
    Str,
};

final class Contains implements Assertion
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        switch (true) {
            case $result->value() instanceof MapInterface:
            case $result->value() instanceof SetInterface:
            case $result->value() instanceof StreamInterface:
            case $result->value() instanceof SequenceInterface:
            case $result->value() instanceof Str:
                if ($result->value()->contains($this->value)) {
                    return $report->success();
                }
                break;

            case \is_string($result->value()):
                if (\mb_strpos($result->value(), $this->value) !== false) {
                    return $report->success();
                }
                break;

            case \is_array($result->value()):
                if (\in_array($this->value, $result->value(), true)) {
                    return $report->success();
                }
                break;

            default:
                return $report->fail('Not a collection');
        }

        return $report->fail('Does not contain expected value');
    }
}
