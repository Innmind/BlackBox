<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Random,
    Set,
    Set\Value,
    Exception\Failure,
};

final class Given
{
    /** @var Set<list<mixed>> */
    private Set $set;

    public function __construct(Set $first, Set ...$rest)
    {
        if (\count($rest) === 0) {
            /** @var Set<list<mixed>> */
            $set = Set\Decorate::immutable(
                /** @psalm-suppress MissingClosureParamType */
                static fn($value): array => [$value],
                $first,
            );
        } else {
            /** @var Set<list<mixed>> */
            $set = Set\Composite::immutable(
                /** @psalm-suppress MissingClosureParamType */
                static fn(...$args): array => $args,
                $first,
                ...$rest,
            );
        }

        /** @var Set<list<mixed>> */
        $this->set = new Set\Randomize($set);
    }

    /**
     * @param positive-int $tests Number of test cases to generate per proof
     * @param callable(): void $pass To print when a test case is successful
     * @param callable('a'|'b'): void $shrinking To print when shrinking a failing test case
     * @param callable(string, TestResult, list<string>): void $fail To print when a test case is failing
     * @param callable(callable(string, TestResult, list<string>): void, Value<list<mixed>>): void $prove
     */
    public function __invoke(
        int $tests,
        bool $enableShrinking,
        Random $rand,
        callable $pass,
        callable $shrinking,
        callable $fail,
        callable $prove
    ): void {
        foreach ($this->set->take($tests)->values($rand) as $values) {
            try {
                $this->test($shrinking, $fail, $prove, $values, $enableShrinking);
                $pass();
            } catch (Failure $e) {
                // no need to run more test cases
                return;
            }
        }
    }

    /**
     * @param callable('a'|'b') $shrinking
     * @param callable(string, TestResult, list<string>): void $fail
     * @param callable(callable(string, TestResult, list<string>): void, Value<list<mixed>>): void $prove
     * @param Value<list<mixed>> $values
     *
     * @throws Failure When the test case failed
     */
    private function test(
        callable $shrinking,
        callable $fail,
        callable $prove,
        Value $values,
        bool $enableShrinking
    ): void {
        try {
            $prove(
                static function(string $reason, TestResult $result, array $trace): void {
                    /** @var list<string> $trace */

                    // we hijack the failure system here to prevent displaying
                    // the symbol that a test case has failed as first we are
                    // going to attempt to shrink the test case
                    throw new Failure($reason, $result, $trace);
                },
                $values,
            );
        } catch (Failure $e) {
            if (!$values->shrinkable() || !$enableShrinking) {
                $fail($e->reason(), $e->result(), $e->trace());

                throw $e;
            }

            $this->shrink($e, $shrinking, $fail, $prove, $values);
        }
    }

    /**
     * @param callable('a'|'b') $shrinking
     * @param callable(string, TestResult, list<string>): void $fail
     * @param callable(callable(string, TestResult, list<string>): void, Value<list<mixed>>): void $prove
     * @param Value<list<mixed>> $values
     *
     * @throws Failure
     */
    private function shrink(
        Failure $previousFailure,
        callable $shrinking,
        callable $fail,
        callable $prove,
        Value $values
    ): void {
        $throwOnFail = static function(string $reason, TestResult $result, array $trace): void {
            /** @var list<string> $trace */

            throw new Failure($reason, $result, $trace);
        };
        $dichotomy = $values->shrink();

        do {
            $currentStrategy = $dichotomy->a();

            try {
                $shrinking('a');
                $prove($throwOnFail, $currentStrategy);
                $currentStrategy = $dichotomy->b();
                $shrinking('b');
                $prove($throwOnFail, $currentStrategy);
            } catch (Failure $e) {
                if ($currentStrategy->shrinkable()) {
                    $dichotomy = $currentStrategy->shrink();
                    $previousFailure = $e;

                    continue;
                }

                // current strategy no longer shrinkable so it means we reached
                // a leaf of our search tree meaning the current exception is the
                // last one we can obtain
                $this->throw($e, $fail);
            }

            // when a and b work then the previous failure has been generated
            // with the smallest values possible
            $this->throw($previousFailure, $fail);
            // we can use an infinite condition here since all exits are covered
        } while (true);
    }

    /**
     * @param callable(string, TestResult, list<string>): void $fail
     *
     * @throws Failure
     */
    private function throw(Failure $e, callable $fail): void
    {
        $fail($e->reason(), $e->result(), $e->trace());

        throw $e;
    }
}
