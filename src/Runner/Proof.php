<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Random;

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
     * @param callable(): void $held To count the number of assertions
     * @param callable(string, string): void $fail
     */
    public function __invoke(
        int $tests,
        Random $rand,
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
            fn(string $reason) => $fail($this->name, $reason),
            fn(callable $fail, ...$args) => ($this->then)(
                $held,
                $fail,
                ($this->when)(...$args),
                ...$args,
            ),
        );
    }
}
