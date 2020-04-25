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
            $this->throw($parentFailure, $values, $test);
        }

        if ($values->shrinkable()) {
            $this->shrink($test, $values, $parentFailure);
        }

        $this->throw($parentFailure, $values, $test);
    }

    private function shrink(
        callable $test,
        Value $values,
        \Throwable $previousFailure
    ): void {
        $previousStrategy = $values;
        $dichotomy = $values->shrink();

        do {
            $currentStrategy = $dichotomy->a();

            try {
                $test(...$currentStrategy->unwrap());
                $currentStrategy = $dichotomy->b();
                $test(...$currentStrategy->unwrap());
            } catch (AssertionFailedError $e) {
                if ($currentStrategy->shrinkable()) {
                    $dichotomy = $currentStrategy->shrink();
                    $previousFailure = $e;
                    $previousStrategy = $currentStrategy;
                    continue;
                }

                // current strategy no longer shrinkable so it means we reached
                // a leaf of our search tree meaning the current exception is the
                // last one we can obtain
                $this->throw($e, $currentStrategy, $test);
            } catch (\Throwable $e) {
                if (($this->expectsException)($e)) {
                    // when inside the process of shrinking we reach a case where
                    // the thrown exception match the test expectation then the
                    // previous case was a special one making the test fail,
                    // otherwise if we rethrow this exception $e it will flag the
                    // test as green even though we found a failing case
                    $this->throw($previousFailure, $previousStrategy, $test);
                }

                if ($currentStrategy->shrinkable()) {
                    $dichotomy = $currentStrategy->shrink();
                    $previousFailure = $e;
                    $previousStrategy = $currentStrategy;
                    continue;
                }

                // current strategy no longer shrinkable so it means we reached
                // a leaf of our search tree meaning the current exception is the
                // last one we can obtain
                $this->throw($e, $currentStrategy, $test);
            }

            // when a and b work then the previous failure has been generated
            // with the smallest values possible
            $this->throw($previousFailure, $previousStrategy, $test);
        // we can use an infinite condition here since all exits are covered
        } while (true);
    }

    private function throw(\Throwable $e, Value $values, callable $test): void
    {
        ($this->recordFailure)($e, $values, $test);

        throw $e;
    }
}
