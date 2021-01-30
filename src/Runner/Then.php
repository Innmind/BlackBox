<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Set\Value;

final class Then
{
    private Hold $hold;

    public function __construct(Hold $hold, Hold ...$rest)
    {
        $this->hold = Hold::all($hold, ...$rest);
    }

    /**
     * @param callable(): void $held To count the number of assertions
     * @param callable(string, list<string>): void $fail
     * @param Value<list<mixed>> $args
     */
    public function __invoke(
        callable $held,
        callable $fail,
        TestResult $result,
        Value $args
    ): void {
        ($this->hold)($held, $fail, $result, ...$args->unwrap());
    }
}
