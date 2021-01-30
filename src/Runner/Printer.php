<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

interface Printer
{
    public function begin(): void;
    public function start(string $proof): void;
    public function pass(string $proof): void;
    public function held(): void;

    /**
     * @param list<string> $trace
     */
    public function fail(
        string $proof,
        string $reason,
        TestResult $result,
        array $trace
    ): void;

    /**
     * @return bool True if there is at least one failure
     */
    public function terminate(): bool;
}
