<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\Printer;

final class BlackBox
{
    /** @var list<string> */
    private array $argv;
    /** @var positive-int */
    private int $tests;
    private bool $enableShrinking;
    private Random $random;
    private Printer $printer;

    /**
     * @param list<string> $argv
     * @param positive-int $tests
     */
    private function __construct(
        array $argv,
        int $tests,
        bool $enableShrinking,
        Random $random,
        Printer $printer
    ) {
        $this->argv = $argv;
        $this->tests = $tests;
        $this->enableShrinking = $enableShrinking;
        $this->random = $random;
        $this->printer = $printer;
    }

    /**
     * @param list<string> $argv
     */
    public static function of(array $argv): self
    {
        return new self(
            $argv,
            100,
            true,
            new Random\RandomInt,
            new Printer\Simple,
        );
    }

    public function disableShrinking(): self
    {
        return new self(
            $this->argv,
            $this->tests,
            false,
            $this->random,
            $this->printer,
        );
    }

    /**
     * @param positive-int $tests
     */
    public function testsPerProof(int $tests): self
    {
        return new self(
            $this->argv,
            $tests,
            $this->enableShrinking,
            $this->random,
            $this->printer,
        );
    }

    public function usePrinter(Printer $printer): self
    {
        return new self(
            $this->argv,
            $this->tests,
            $this->enableShrinking,
            $this->random,
            $printer,
        );
    }

    /**
     * @return 0|1 The exit code to return by the script
     */
    public function tryToProve(string $pathToProofs): int
    {
        require_once __DIR__.'/Runner/functions.php';

        $run = new Runner(
            $this->tests,
            $this->enableShrinking,
            $this->random,
            $this->printer,
            $this->argv[1] ?? '',
        );

        return (int) $run($pathToProofs);
    }
}
