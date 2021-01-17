<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Hold
{
    /** @var callable(callable(): void, callable(string): void, TestResult, ...mixed): void */
    private $assertion;

    /**
     * @param callable(callable(): void, callable(string): void, TestResult, ...mixed): void $assertion
     */
    public function __construct(callable $assertion)
    {
        $this->assertion = $assertion;
    }

    /**
     * @param callable(): void $held To count the number of assertions
     * @param callable(string): void $fail
     * @param mixed $args
     */
    public function __invoke(
        callable $held,
        callable $fail,
        TestResult $result,
        ...$args
    ): void {
        ($this->assertion)($held, $fail, $result, ...$args);
    }

    public static function all(self $hold, self ...$rest): self
    {
        \array_unshift($rest, $hold);

        /** @psalm-suppress MissingClosureParamType */
        return new self(static function(
                callable $held,
                callable $fail,
                TestResult $result,
                ...$args
            ) use ($rest): void {
            foreach ($rest as $hold) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $hold($held, $fail, $result, ...$args);
            }
        });
    }
}
