<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Random,
    Runner\Runner\WithShrinking,
    Runner\Runner\WithoutShrinking,
    Exception\EmptySet,
};

/**
 * @internal
 */
final class Runner
{
    /**
     * @param \Generator<Proof> $proofs
     * @param int<1, max> $scenariiPerProof
     */
    private function __construct(
        private Random $random,
        private Printer $print,
        private IO $output,
        private IO $error,
        private WithShrinking|WithoutShrinking $run,
        private \Generator $proofs,
        private int $scenariiPerProof,
        private bool $disableMemoryLimit,
        private bool $stopOnFailure,
        private bool $failWhenNoAssertions,
    ) {
    }

    /**
     * @return \Generator<Proof\Scenario\Failure>
     */
    public function __invoke(
        Stats $stats,
        ?CodeCoverage $codeCoverage,
    ): \Generator {
        if ($this->disableMemoryLimit) {
            \ini_set('memory_limit', '-1');
        }

        $coverage = $codeCoverage?->build();
        $this->print->start($this->output, $this->error);
        $coverage?->loadProof();

        while ($this->proofs->valid()) {
            /** @var Proof */
            $proof = $this->proofs->current();
            $coverage?->stop();

            $stats->incrementProofs();
            $print = $this->print->proof(
                $this->output,
                $this->error,
                $proof->name(),
                $proof->tags(),
            );
            $coverage?->start($proof->name());

            try {
                $scenarii = $proof
                    ->scenarii($this->scenariiPerProof)
                    ->values($this->random);

                foreach ($scenarii as $scenario) {
                    $debug = Assert\Debug::new();
                    $assert = Assert::of($stats, $debug);
                    $stats->incrementScenarii();
                    $assertions = $stats->assertions();

                    try {
                        ($this->run)(
                            $print,
                            $this->output,
                            $this->error,
                            $assert,
                            $scenario,
                            $debug,
                        );

                        if ($this->failWhenNoAssertions && $stats->assertions() === $assertions) {
                            throw Proof\Scenario\Failure::of(
                                Assert\Failure::of(Assert\Failure\Truth::of(
                                    'The proof did not make any assertion',
                                )),
                                $scenario,
                                $debug,
                            );
                        }
                    } catch (Proof\Scenario\Failure $e) {
                        // We unset the initial scenario here to free any object
                        // that may be kept in a WeakReference or WeakMap inside
                        // an object inside. This prevents displaying values
                        // that were generated during the shrinking but no
                        // longer used in the final failing scenario.
                        unset($scenario);

                        $stats->incrementFailures();
                        $print->failed(
                            $this->output,
                            $this->error,
                            $e,
                        );

                        yield $e;

                        break;
                    }
                }
            } catch (EmptySet $e) {
                $print->emptySet($this->output, $this->error);

                break;
            }

            $print->end($this->output, $this->error);
            $coverage?->stop();

            if ($this->stopOnFailure && !$stats->successful()) {
                break;
            }

            $coverage?->loadProof();
            $this->proofs->next();
        }

        $this->print->end($this->output, $this->error, $stats);
        $coverage?->dump();
    }

    /**
     * @param \Generator<Proof> $proofs
     * @param int<1, max> $scenariiPerProof
     */
    public static function of(
        Random $random,
        Printer $print,
        IO $output,
        IO $error,
        WithShrinking|WithoutShrinking $run,
        \Generator $proofs,
        int $scenariiPerProof,
        bool $disableMemoryLimit,
        bool $stopOnFailure,
        bool $failWhenNoAssertions,
    ): self {
        return new self(
            $random,
            $print,
            $output,
            $error,
            $run,
            $proofs,
            $scenariiPerProof,
            $disableMemoryLimit,
            $stopOnFailure,
            $failWhenNoAssertions,
        );
    }
}
