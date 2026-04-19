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
    /** @var int<1, max> */
    private int $scenariiPerProof;
    private bool $disableMemoryLimit;
    private bool $stopOnFailure;
    private bool $failWhenNoAssertions;
    /** @var ?list<\UnitEnum> */
    private ?array $tags;

    /**
     * @param \Closure(string): ?\UnitEnum $parseTag
     * @param list<string> $args
     * @param int<1, max> $scenariiPerProof
     * @param ?list<\UnitEnum> $tags
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
        bool $disableMemoryLimit,
        bool $stopOnFailure,
        bool $failWhenNoAssertions,
        ?array $tags,
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
        $this->disableMemoryLimit = $disableMemoryLimit;
        $this->stopOnFailure = $stopOnFailure;
        $this->failWhenNoAssertions = $failWhenNoAssertions;
        $this->tags = $tags;
    }

    /**
     * @param list<string> $args
     */
    #[\NoDiscard]
    public static function new(array $args): self
    {
        return new self(
            Random::default,
            Printer\Standard::new(),
            IO\Standard::output,
            IO\Standard::error,
            WithShrinking::keepErrorType(),
            Tag::of(...),
            null,
            $args,
            100,
            false,
            false,
            true,
            null,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * This will keep shrinking even if the type of error changes
     */
    #[\NoDiscard]
    public function useExhaustiveShrinking(): self
    {
        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            WithShrinking::exhaustive(),
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): ?\UnitEnum $parser
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $count
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            true,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            true,
            $this->failWhenNoAssertions,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            false,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    #[\NoDiscard]
    public function filterOnTags(\UnitEnum ...$tags): self
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
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $tags,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(self): self $map
     */
    #[\NoDiscard]
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
    #[\NoDiscard]
    public function when(bool $active, callable $map): self
    {
        return match ($active) {
            true => $this->map($map),
            false => $this,
        };
    }

    /**
     * @param callable(Prove): \Generator<Proof> $proofs
     */
    #[\NoDiscard]
    public function tryToProve(callable $proofs): Result
    {
        if (\is_null($this->tags)) {
            $tags = \array_map($this->parseTag, $this->args);
            $tags = \array_filter($tags, static fn($tag) => $tag instanceof \UnitEnum);
        } else {
            $tags = $this->tags;
        }

        $filter = Filter::new()->onTags(...$tags);

        $run = Runner::of(
            $this->random,
            $this->printer,
            $this->output,
            $this->error,
            $this->runner,
            $filter($proofs(Prove::new())),
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
        $stats = Stats::new();

        $run($stats, $this->codeCoverage);

        return Result::of($stats);
    }
}
