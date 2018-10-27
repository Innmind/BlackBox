<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Test;

use Innmind\BlackBox\{
    Test as TestInterface,
    Given,
    When,
    Then,
};

final class Test implements TestInterface
{
    private $name;
    private $given;
    private $when;
    private $then;

    public function __construct(Name $name, Given $given, When $when, Then $then)
    {
        $this->name = $name;
        $this->given = $given;
        $this->when = $when;
        $this->then = $then;
    }

    public function __invoke(): Report
    {
        $report = new Report($this->name);
        $scenarios = $this->given->scenarios();

        foreach ($scenarios as $scenario) {
            if ($report->failed()) {
                return $report;
            }

            $result = ($this->when)($scenario);

            $report->add(
                $scenario,
                $result,
                ($this->then)($result, $scenario)
            );
        }

        return $report;
    }
}
