<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Test;

use Innmind\BlackBox\{
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
    Then\Failure,
    Exception\LogicException,
};

final class Report
{
    private $name;
    private $assertions = 0;
    private $failure;
    private $failedScenario;
    private $failedResult;

    public function __construct(Name $name)
    {
        $this->name = $name;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function add(Scenario $given, Result $result, ScenarioReport $report): self
    {
        if ($this->failed()) {
            throw new LogicException('No report must be added after a failure');
        }

        if ($report->failed()) {
            $this->failure = $report->failure();
            $this->failedScenario = $given;
            $this->failedResult = $result;
        }

        $this->assertions += $report->assertions();

        return $this;
    }

    public function assertions(): int
    {
        return $this->assertions;
    }

    public function failed(): bool
    {
        return $this->failure instanceof Failure;
    }

    public function failure(): Failure
    {
        return $this->failure;
    }

    public function failedScenario(): Scenario
    {
        return $this->failedScenario;
    }

    public function failedResult(): Result
    {
        return $this->failedResult;
    }
}
