<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Random,
    Set\Value,
};

/**
 * Bear in mind this is not a formal proof (in the mathematical sense) but an
 * attempt to prove a property
 */
final class Proof
{
    private string $name;
    private Given $given;
    private When $when;
    private Then $then;

    public function __construct(
        string $name,
        Given $given,
        When $when,
        Then $then
    ) {
        $this->name = $name;
        $this->given = $given;
        $this->when = $when;
        $this->then = $then;
    }

    /**
     * @param positive-int $tests Number of test cases to generate per proof
     * @param callable(string): void $pass To print when a test case is successful
     * @param callable(): void $held To count the number of assertions
     * @param callable(string, 'a'|'b'): void $shrinking To print when shrinking a failing test case
     * @param callable(string, string, TestResult, list<string>): void $fail To print when a test case is failing
     */
    public function __invoke(
        int $tests,
        bool $enableShrinking,
        Random $rand,
        callable $pass,
        callable $held,
        callable $shrinking,
        callable $fail
    ): void {
        /**
         * @psalm-suppress MissingClosureParamType
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress ArgumentTypeCoercion For the shrinking strategy
         */
        ($this->given)(
            $tests,
            $enableShrinking,
            $rand,
            fn() => $pass($this->name),
            fn(string $strategy) => $shrinking($this->name, $strategy),
            fn(string $reason, TestResult $result, array $trace) => $fail($this->name, $reason, $result, $trace),
            function(callable $fail, Value $args) use ($held): void {
                $testResult = ($this->when)($args);

                ($this->then)(
                    $held,
                    static function(string $reason, array $trace) use ($fail, $testResult): void {
                        $fail($reason, $testResult, $trace);
                    },
                    $testResult,
                    $args,
                );
            },
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function matches(string $filter): bool
    {
        if ($filter === '') {
            return true;
        }

        return \strpos($this->name, $filter) !== false;
    }
}
