<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\Set\Value;
use PHPUnit\Framework\AssertionFailedError;

final class TestRunner
{
    private \Closure $recordFailure;
    private \Closure $expectsException;
    private bool $shrinkingDisabled;

    /**
     * @param callable(\Throwable): bool $expectsException
     */
    public function __construct(
        callable $recordFailure,
        callable $expectsException,
        bool $disableShrinking = false
    ) {
        $this->recordFailure = \Closure::fromCallable($recordFailure);
        $this->expectsException = \Closure::fromCallable($expectsException);
        $this->shrinkingDisabled = $disableShrinking;
    }

    public function __invoke(callable $test, Value $values): void
    {
        try {
            $test(...$values->unwrap());
        } catch (AssertionFailedError $e) {
            $this->tryToShrink($test, $values, $e);
        } catch (\Throwable $e) {
            if (($this->expectsException)($e)) {
                throw $e;
            }

            $this->tryToShrink($test, $values, $e);
        }
    }

    private function tryToShrink(
        callable $test,
        Value $values,
        \Throwable $parentFailure
    ): void {
        if ($this->shrinkingDisabled) {
            $this->throw($parentFailure, $values);
        }

        if ($values->shrinkable()) {
            $this->shrink($test, $values, $parentFailure);
        }

        $this->throw($parentFailure, $values);
    }

    private function shrink(
        callable $test,
        Value $values,
        \Throwable $parentFailure
    ): void {
        $dichotomy = $values->shrink();

        $this($test, $dichotomy->a());
        $this($test, $dichotomy->b());

        // if both strategies doesn't raise an exception then it means the smallest
        // failing strategy is the parent value so we throw the parent assertion
        // failure exception that wil bubble up to the PHPUnit runner
        $this->throw($parentFailure, $values);
    }

    private function throw(\Throwable $e, Value $values): void
    {
        ($this->recordFailure)($e, $values);

        throw $e;
    }
}
