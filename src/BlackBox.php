<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\Printer;

final class BlackBox
{
    /** @var positive-int */
    private int $tests;
    private bool $enableShrinking;
    private Random $random;
    private Printer $printer;

    /**
     * @param positive-int $tests
     */
    private function __construct(
        int $tests,
        bool $enableShrinking,
        Random $random,
        Printer $printer
    ) {
        $this->tests = $tests;
        $this->enableShrinking = $enableShrinking;
        $this->random = $random;
        $this->printer = $printer;
    }

    public static function of(): self
    {
        return new self(100, true, new Random\RandomInt, new Printer\Simple);
    }

    public function disableShrinking(): self
    {
        return new self($this->tests, false, $this->random, $this->printer);
    }

    /**
     * @param positive-int $tests
     */
    public function testsPerProof(int $tests): self
    {
        return new self($tests, $this->enableShrinking, $this->random, $this->printer);
    }

    public function usePrinter(Printer $printer): self
    {
        return new self($this->tests, $this->enableShrinking, $this->random, $printer);
    }

    /**
     * @return positive-int The exit code to return by the script
     */
    public function tryToProve(string $pathToProofs): int
    {
        $run = new Runner(
            $this->tests,
            $this->enableShrinking,
            $this->random,
            $this->printer,
        );

        return (int) $run($pathToProofs);
    }
}
