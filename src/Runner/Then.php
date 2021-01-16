<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Then
{
    private Hold $hold;

    public function __construct(Hold $hold, Hold ...$rest)
    {
        $this->hold = Hold::all($hold, ...$rest);
    }

    /**
     * @param callable(): void $pass
     * @param callable(string): void $fail
     * @param mixed $args
     */
    public function __invoke(
        callable $pass,
        callable $fail,
        TestResult $result,
        ...$args
    ): void {
        ($this->hold)($pass, $fail, $result, ...$args);
    }
}
