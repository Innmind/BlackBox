<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer;

use Innmind\BlackBox\Runner\{
    Printer,
    Arguments,
};

final class Simple implements Printer
{
    private int $countProofs = 0;
    private int $countTestCases = 0;
    private int $countAssertions = 0;
    private int $countFailures = 0;

    public function begin(): void
    {
        $this->countProofs = 0;
        $this->countTestCases = 0;
        $this->countAssertions = 0;
        $this->countFailures = 0;
    }

    public function start(string $proof): void
    {
        ++$this->countProofs;
    }

    public function pass(): void
    {
        ++$this->countTestCases;
        echo '.';

        $this->breakLine();
    }

    public function held(): void
    {
        ++$this->countAssertions;
    }

    public function fail(string $proof, string $reason, Arguments $arguments): void
    {
        ++$this->countTestCases;
        ++$this->countFailures;
        echo 'F';

        $this->breakLine();
    }

    public function terminate(): bool
    {
        echo "\n";
        echo "Proofs:               {$this->countProofs}\n";
        echo "Test cases generated: {$this->countTestCases}\n";
        echo "Assertions:           {$this->countAssertions}\n";
        echo "Failures:             {$this->countFailures}\n";

        return $this->countFailures > 0;
    }

    private function breakLine(): void
    {
        if ($this->countTestCases % 50 === 0) {
            echo "\n";
        }
    }
}
