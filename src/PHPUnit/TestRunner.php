<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\Set\Value;
use PHPUnit\Framework\AssertionFailedError;

final class TestRunner
{
    private bool $shrinkingDisabled;

    public function __construct(bool $disableShrinking = false)
    {
        return $this->shrinkingDisabled = $disableShrinking;
    }

    public function __invoke(callable $test, Value $values): void
    {
        try {
            $test(...$values->unwrap());
        } catch (AssertionFailedError $e) {
            if ($this->shrinkingDisabled) {
                throw $e;
            }

            if ($values->shrinkable()) {
                $this->shrink($test, $values, $e);
            } else {
                throw $e;
            }
        }
    }

    private function shrink(
        callable $test,
        Value $values,
        AssertionFailedError $parentFailure
    ): void {
        $dichotomy = $values->shrink();

        $this($test, $dichotomy->a());
        $this($test, $dichotomy->b());

        ResultPrinterV8::record($parentFailure, $values);

        // if both strategies doesn't raise an exception then it means the smallest
        // failing strategy is the parent value so we throw the parent assertion
        // failure exception that wil bubble up to the PHPUnit runner
        throw $parentFailure;
    }
}
