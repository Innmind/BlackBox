<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\Scenario,
    When\Result,
};
use Innmind\OperatingSystem\OperatingSystem;

final class When
{
    private $test;

    public function __construct(callable $test)
    {
        // the wrapping is to make sure there is no _$this_ inside the callable;
        $this->test = \Closure::bind($test, null);
    }

    public function __invoke(
        OperatingSystem $os,
        Scenario $scenario
    ): Result {
        try {
            $start = $os->clock()->now();
            $result = ($this->test)($scenario);
        } catch (\Throwable $e) {
            $result = $e;
        } finally {
            return new Result(
                $result,
                $os->clock()->now()->elapsedSince($start)
            );
        }
    }
}
