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
     * @param callable(string, Arguments): void $fail To print when a test case is failing
     * @param callable(callable(string, Arguments): void, Value<list<mixed>>): void $prove
     */
    public function __invoke(
        int $tests,
        bool $enableShrinking,
        Random $rand,
        callable $pass,
        callable $fail,
        callable $prove
    ): void {
        foreach ($this->set->take($tests)->values($rand) as $values) {
            try {
                $this->test($fail, $prove, $values, $enableShrinking);
                $pass();
            } catch (Failure $e) {
                // no need to run more test cases
                return;
            }
        }
    }

    /**
     * @param callable(string, Arguments): void $fail
     * @param callable(callable(string, Arguments): void, Value<list<mixed>>): void $prove
     * @param Value<list<mixed>> $values
     *
     * @throws Failure When the test case failed
     */
    private function test(
        callable $fail,
        callable $prove,
        Value $values,
        bool $enableShrinking
    ): void {
        try {
            $prove(
                static function(string $reason, Arguments $arguments): void {
                    // we hijack the failure system here to prevent displaying
                    // the symbol that a test case has failed as first we are
                    // going to attempt to shrink the test case
                    throw new Failure($reason, $arguments);
                },
                $values,
            );
        } catch (Failure $e) {
            if (!$values->shrinkable() || !$enableShrinking) {
                $fail($e->reason(), $e->arguments());

                throw $e;
            }

            $this->shrink($e, $fail, $prove, $values);
        }
    }

    /**
     * @param callable(string, Arguments): void $fail
     * @param callable(callable(string, Arguments): void, Value<list<mixed>>): void $prove
     * @param Value<list<mixed>> $values
     *
     * @throws Failure
     */
    private function shrink(
        Failure $previousFailure,
        callable $fail,
        callable $prove,
        Value $values
    ): void {
        $throwOnFail = static function(string $reason, Arguments $arguments): void {
            throw new Failure($reason, $arguments);
        };
        $previousStrategy = $values;
        $dichotomy = $values->shrink();

        do {
            $currentStrategy = $dichotomy->a();

            try {
                $prove($throwOnFail, $currentStrategy);
                $currentStrategy = $dichotomy->b();
                $prove($throwOnFail, $currentStrategy);
            } catch (Failure $e) {
                if ($currentStrategy->shrinkable()) {
                    $dichotomy = $currentStrategy->shrink();
                    $previousFailure = $e;
                    $previousStrategy = $currentStrategy;

                    continue;
                }

                // current strategy no longer shrinkable so it means we reached
                // a leaf of our search tree meaning the current exception is the
                // last one we can obtain
                $this->throw($e, $currentStrategy, $fail);
            }

            // when a and b work then the previous failure has been generated
            // with the smallest values possible
            $this->throw($previousFailure, $previousStrategy, $fail);
            // we can use an infinite condition here since all exits are covered
        } while (true);
    }

    /**
     * @param Value<list<mixed>> $values
     * @param callable(string, Arguments): void $fail
     *
     * @throws Failure
     */
    private function throw(Failure $e, Value $values, callable $fail): void
    {
        $fail($e->reason(), $e->arguments());

        throw $e;
    }
}
