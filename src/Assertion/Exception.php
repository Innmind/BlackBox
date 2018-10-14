<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Str;

final class Exception implements Assertion
{
    private $class;
    private $message;
    private $code;

    public function __construct(string $class, string $message = null, int $code = null)
    {
        $this->class = $class;
        $this->message = $message;
        $this->code = $code;
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (!$result->value() instanceof \Throwable) {
            return $report->fail('Not an exception');
        }

        if (!$result->value() instanceof $this->class) {
            return $report->fail("Not exception {$this->class}");
        }

        if (\is_string($this->message) && !Str::of($result->value()->getMessage())->contains($this->message)) {
            return $report->fail("Exception message doesn't contain \"{$this->message}\"");
        }

        if (is_int($this->code) && $result->value()->getCode() !== $this->code) {
            return $report->fail("Exception code is different than {$this->code}");
        }

        return $report->success();
    }
}
