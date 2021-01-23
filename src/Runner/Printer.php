<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

interface Printer
{
    public function begin(): void;
    public function start(string $proof): void;
    public function pass(string $proof): void;
    public function held(): void;
    public function fail(string $proof, string $reason, Arguments $arguments): void;

    /**
     * @return bool True if there is at least one failure
     */
    public function terminate(): bool;
}
