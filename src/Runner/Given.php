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
        foreach ($this->set->take($tests)->values($rand) as $value) {
            try {
                $this->test($fail, $prove, $value);
            } catch (Failure $e) {
                // no need to run more test cases
                return;
            }
        }
    }

    /**
     * @param callable(string): void $fail
     * @param callable(callable(string): void, ...mixed): void $prove
     * @param Value<list<mixed>> $value
     *
     * @throws Failure When the test case failed
     */
    private function test(callable $fail, callable $prove, Value $value): void
    {
        try {
            $prove(
                static function(string $reason): void {
                    // we hijack the failure system here to prevent displaying
                    // the symbol that a test case has failed as first we are
                    // going to attempt to shrink the test case
                    throw new Failure($reason);
                },
                ...$value->unwrap(),
            );
        } catch (Failure $e) {
            // TODO shrink
            $fail($e->getMessage());

            throw $e;
        }
    }
}
