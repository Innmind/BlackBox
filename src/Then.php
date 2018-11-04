<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Immutable\Stream;

final class Then
{
    private $assertions;

    public function __construct(Assertion $assertion, Assertion ...$assertions)
    {
        $this->assertions = Stream::of(Assertion::class, $assertion, ...$assertions);
    }

    public function __invoke(
        OperatingSystem $os,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        return $this->assertions->reduce(
            new ScenarioReport,
            static function(ScenarioReport $report, Assertion $assert) use ($os, $result, $scenario): ScenarioReport {
                if ($report->failed()) {
                    return $report;
                }

                return $assert($os, $report, $result, $scenario);
            }
        );
    }
}
