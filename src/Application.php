<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\{
    Printer,
    IO,
    Proof,
    Runner,
    Runner\WithShrinking,
    Runner\WithoutShrinking,
    Result,
    Stats,
    Assert,
};

final class Application
{
    private Random $random;
    private Printer $printer;
    private IO $output;
    private IO $error;
    private WithShrinking|WithoutShrinking $runner;

    private function __construct(
        Random $random,
        Printer $printer,
        IO $output,
        IO $error,
        WithShrinking|WithoutShrinking $runner,
    ) {
        $this->random = $random;
        $this->printer = $printer;
        $this->output = $output;
        $this->error = $error;
        $this->runner = $runner;
    }

    public static function new(): self
    {
        return new self(
            Random::default,
            Printer\Default::new(),
            IO\Standard::output,
            IO\Standard::error,
            new WithShrinking,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function useRandom(Random $random): self
    {
        return new self(
            $random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function usePrinter(Printer $printer): self
    {
        return new self(
            $this->random,
            $printer,
            $this->output,
            $this->error,
            $this->runner,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function displayOutputVia(IO $output): self
    {
        return new self(
            $this->random,
            $this->printer,
            $output,
            $this->error,
            $this->runner,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function displayErrorVia(IO $error): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $error,
            $this->runner,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function disableShrinking(): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            new WithoutShrinking,
        );
    }

    /**
     * @param callable(): \Generator<Proof> $proofs
     */
    public function tryToProve(callable $proofs): Result
    {
        $run = Runner::of(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $proofs(),
        );
        $stats = Stats::new();
        $assert = Assert::of($stats);

        $run($stats, $assert);

        return Result::of($stats);
    }
}
