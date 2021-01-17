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
     * @param callable(): void $pass To print when a test case is successful
     * @param callable(): void $held To count the number of assertions
     * @param callable(string, string, Arguments): void $fail To print when a test case is failing
     */
    public function __invoke(
        int $tests,
        Random $rand,
        callable $pass,
        callable $held,
        callable $fail
    ): void {
        /**
         * @psalm-suppress MissingClosureParamType
         * @psalm-suppress MixedArgumentTypeCoercion
         */
        ($this->given)(
            $tests,
            $rand,
            $pass,
            fn(string $reason, Arguments $arguments) => $fail($this->name, $reason, $arguments),
            function(callable $fail, Value $args) use ($held): void {
                $testResult = ($this->when)($args);

                ($this->then)(
                    $held,
                    static function(string $reason) use ($fail, $testResult): void {
                        $fail($reason, $testResult->arguments());
                    },
                    $testResult,
                    $args,
                );
            },
        );
    }
}
