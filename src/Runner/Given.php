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
     * @param callable(string): void $fail
     * @param callable(...mixed): void $prove
     */
    public function __invoke(
        int $tests,
        Random $rand,
        callable $fail,
        callable $prove
    ): void {
        foreach ($this->set->take($tests)->values($rand) as $values) {
            try {
                $this->test($fail, $prove, $values);
            } catch (Failure $e) {
                // no need to run more test cases
                return;
            }
        }
    }

    /**
     * @param callable(string): void $fail
     * @param callable(callable(string): void, ...mixed): void $prove
     * @param Value<list<mixed>> $values
     *
     * @throws Failure When the test case failed
     */
    private function test(callable $fail, callable $prove, Value $values): void
    {
        try {
            $prove(
                static function(string $reason): void {
                    // we hijack the failure system here to prevent displaying
                    // the symbol that a test case has failed as first we are
                    // going to attempt to shrink the test case
                    throw new Failure($reason);
                },
                ...$values->unwrap(),
            );
        } catch (Failure $e) {
            if (!$values->shrinkable()) {
                $fail($e->getMessage());

                throw $e;
            }

            $this->shrink($e, $fail, $prove, $values);
        }
    }

    /**
     * @param callable(string): void $fail
     * @param callable(callable(string): void, ...mixed): void $prove
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
        $throwOnFail = static function(string $reason): void {
            throw new Failure($reason);
        };
        $previousStrategy = $values;
        $dichotomy = $values->shrink();

        do {
            $currentStrategy = $dichotomy->a();

            try {
                $prove($throwOnFail, ...$currentStrategy->unwrap());
                $currentStrategy = $dichotomy->b();
                $prove($throwOnFail, ...$currentStrategy->unwrap());
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
     * @param callable(string): void $fail
     *
     * @throws Failure
     */
    private function throw(Failure $e, Value $values, callable $fail): void
    {
        $fail($e->getMessage());

        throw $e;
    }
}