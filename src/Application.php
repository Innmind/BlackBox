<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\{
    Printer,
    IO,
    Proof,
    Runner,
    Runner\Strategy,
    Result,
    Stats,
    Filter,
    CodeCoverage,
};

final class Application
{
    /**
     * @param \Closure(string): ?\UnitEnum $parseTag
     * @param list<string> $args
     * @param int<1, max> $scenariiPerProof
     * @param ?list<\UnitEnum> $tags
     * @param \Closure(Proof): Proof $mapProof
     */
    private function __construct(
        private Random $random,
        private Printer $printer,
        private IO $output,
        private Strategy $runner,
        private \Closure $parseTag,
        private ?CodeCoverage $codeCoverage,
        private array $args,
        private int $scenariiPerProof,
        private bool $disableMemoryLimit,
        private bool $stopOnFailure,
        private bool $failWhenNoAssertions,
        private ?array $tags,
        private \Closure $mapProof,
    ) {
    }

    /**
     * @param list<string> $args
     */
    #[\NoDiscard]
    public static function new(array $args): self
    {
        return new self(
            Random::default,
            Printer::new(),
            IO\Standard::output,
            Strategy::keepErrorType,
            Tag::of(...),
            null,
            $args,
            100,
            false,
            false,
            true,
            null,
            static fn(Proof $proof) => $proof,
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
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(Printer): Printer $map
     */
    #[\NoDiscard]
    public function mapPrinter(callable $map): self
    {
        /** @psalm-suppress ImpureFunctionCall */
        return new self(
            $this->random,
            $map($this->printer),
            $this->output,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function displayVia(IO $output): self
    {
        return new self(
            $this->random,
            $this->printer,
            $output,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function disableShrinking(): self
    {
        $previous = $this->mapProof;

        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            static fn($proof) => $previous($proof)->disableShrinking(),
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
            Strategy::exhaustive,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
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
        $previous = $this->parseTag;

        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->runner,
            static fn(string $name) => $parser($name) ?? $previous($name),
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
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
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $count,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
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
            $this->runner,
            $this->parseTag,
            $codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
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
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            true,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
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
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            true,
            $this->failWhenNoAssertions,
            $this->tags,
            $this->mapProof,
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
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            false,
            $this->tags,
            $this->mapProof,
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
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $tags,
            $this->mapProof,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(Proof): Proof $map
     */
    #[\NoDiscard]
    public function mapProof(callable $map): self
    {
        $previous = $this->mapProof;

        return new self(
            $this->random,
            $this->printer,
            $this->output,
            $this->runner,
            $this->parseTag,
            $this->codeCoverage,
            $this->args,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
            $this->tags,
            static fn($proof) => $map($previous($proof)),
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
        $failures = $this->failures($proofs);

        while ($failures->valid()) {
            $failures->next();
        }

        return $failures->getReturn();
    }

    /**
     * @internal
     *
     * @param callable(Prove): \Generator<Proof> $proofs
     *
     * @return \Generator<int, Proof\Scenario\Failure, null, Result>
     */
    #[\NoDiscard]
    public function failures(callable $proofs): \Generator
    {
        if (\is_null($this->tags)) {
            $tags = \array_map($this->parseTag, $this->args);
            $tags = \array_filter($tags, static fn($tag) => $tag instanceof \UnitEnum);
        } else {
            $tags = $this->tags;
        }

        $filter = Filter::new()->onTags(...$tags);
        $proofs = $filter($proofs(Prove::new()));

        $map = $this->mapProof;
        $proofs = (static function() use ($proofs, $map) {
            foreach ($proofs as $proof) {
                yield $map($proof);
            }
        })();

        $run = Runner::of(
            $this->random,
            $this->printer,
            $this->output,
            $this->runner,
            $proofs,
            $this->scenariiPerProof,
            $this->disableMemoryLimit,
            $this->stopOnFailure,
            $this->failWhenNoAssertions,
        );
        $stats = Stats::new();

        foreach ($run($stats, $this->codeCoverage) as $failure) {
            yield $failure;
        }

        return Result::of($stats);
    }
}
