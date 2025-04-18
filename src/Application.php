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
    Filter,
    CodeCoverage,
};

final class Application
{
    private Random $random;
    private Printer $printer;
    private IO $output;
    private IO $error;
    private WithShrinking|WithoutShrinking $runner;
    /** @var \Closure(string): ?\UnitEnum */
    private \Closure $parseTag;
    private ?CodeCoverage $codeCoverage;
    /** @var list<string> */
    private array $args;
    /** @var positive-int */
    private int $scenariiPerProof;
    private bool $useGlobalFunctions;
    private bool $disableMemoryLimit;
    private bool $stopOnFailure;
    private bool $failWhenNoAssertions;

    /**
     * @param \Closure(string): ?\UnitEnum $parseTag
     * @param list<string> $args
     * @param positive-int $scenariiPerProof
     */
    private function __construct(
        Random $random,
        Printer $printer,
        IO $output,
        IO $error,
        WithShrinking|WithoutShrinking $runner,
        \Closure $parseTag,
        ?CodeCoverage $codeCoverage,
        array $args,
        int $scenariiPerProof,
        bool $useGlobalFunctions,
        bool $disableMemoryLimit,
        bool $stopOnFailure,
        bool $failWhenNoAssertions,
    ) {
        $this->random = $random;
        $this->printer = $printer;
        $this->output = $output;
        $this->error = $error;
        $this->runner = $runner;
        $this->parseTag = $parseTag;
        $this->codeCoverage = $codeCoverage;
        $this->args = $args;
        $this->scenariiPerProof = $scenariiPerProof;
        $this->useGlobalFunctions = $useGlobalFunctions;
        $this->disableMemoryLimit = $disableMemoryLimit;
        $this->stopOnFailure = $stopOnFailure;
        $this->failWhenNoAssertions = $failWhenNoAssertions;
    }

    /**
     * @param list<string> $args
     */
    public static function new(array $args): self
    {
        return new self(
            Random::default,
            Printer\Standard::new(),
            IO\Standard::output,
            IO\Standard::error,
            new WithShrinking,
            Tag::of(...),
            null,
            $args,
            100,
            true,
            false,
            false,
            true,
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
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
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
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
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
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
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
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
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
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): ?\UnitEnum $parser
     */
    public function parseTagWith(callable $parser): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            fn(string $name) => $parser($name) ?? ($this->parseTag)($name),
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $count
     */
    public function scenariiPerProof(int $count): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $count,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function codeCoverage(CodeCoverage $codeCoverage): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $this->parseTag,
            $codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function disableGlobalFunctions(): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            false,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function disableMemoryLimit(): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            true,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function stopOnFailure(): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            true,
            $this->failWhenNoAssertions,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function allowProofsToNotMakeAnyAssertions(): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->useGlobalFunctions,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            false,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(self): self $map
     */
    public function map(callable $map): self
    {
        /** @psalm-suppress ImpureFunctionCall */
        return $map($this);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(self): self $map
     */
    public function when(bool $active, callable $map): self
    {
        return match ($active) {
            true => $this->map($map),
            false => $this,
        };
    }

    /**
     * @param callable(): \Generator<Proof> $proofs
     */
    public function tryToProve(callable $proofs): Result
    {
        if ($this->useGlobalFunctions) {
            require_once __DIR__.'/Runner/global.php';
        }

        $tags = \array_map($this->parseTag, $this->args);
        $tags = \array_filter($tags, static fn($tag) => $tag instanceof \UnitEnum);
        $filter = Filter::new()->onTags(...$tags);

        $run = Runner::of(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $filter($proofs()),
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
        $stats = Stats::new();
        $assert = Assert::of($stats);

        $run($stats, $assert, $this->codeCoverage);

        return Result::of($stats);
    }
}
