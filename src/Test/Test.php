<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Test;

use Innmind\BlackBox\{
    Test as TestInterface,
    Given,
    Given\Scenario,
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
        return $this
            ->given
            ->scenarios()
            ->reduce(
                new Report($this->name),
                function(Report $report, Scenario $scenario): Report {
                    $result = ($this->when)($scenario);

                    return $report->add(
                        $scenario,
                        $result,
                        ($this->then)($result, $scenario)
                    );
                }
            );
    }
}
