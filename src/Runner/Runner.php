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
    private Random $random;
    private Printer $print;
    private IO $output;
    private IO $error;
    private WithShrinking|WithoutShrinking $run;
    /** @var \Generator<Proof> */
    private \Generator $proofs;
    /** @var positive-int */
    private int $scenariiPerProof;
    private bool $disableMemoryLimit;
    private bool $stopOnFailure;

    /**
     * @param \Generator<Proof> $proofs
     * @param positive-int $scenariiPerProof
     */
    private function __construct(
        Random $random,
        Printer $print,
        IO $output,
        IO $error,
        WithShrinking|WithoutShrinking $run,
        \Generator $proofs,
        int $scenariiPerProof,
        bool $disableMemoryLimit,
        bool $stopOnFailure,
    ) {
        $this->random = $random;
        $this->print = $print;
        $this->output = $output;
        $this->error = $error;
        $this->run = $run;
        $this->proofs = $proofs;
        $this->scenariiPerProof = $scenariiPerProof;
        $this->disableMemoryLimit = $disableMemoryLimit;
        $this->stopOnFailure = $stopOnFailure;
    }

    public function __invoke(
        Stats $stats,
        Assert $assert,
        ?CodeCoverage $codeCoverage,
    ): void {
        if ($this->disableMemoryLimit) {
            \ini_set('memory_limit', '-1');
        }

        $coverage = $codeCoverage?->build();
        $this->print->start($this->output, $this->error);

        foreach ($this->proofs as $proof) {
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
                    $stats->incrementScenarii();

                    try {
                        ($this->run)(
                            $print,
                            $this->output,
                            $this->error,
                            $assert,
                            $scenario,
                        );
                    } catch (Proof\Scenario\Failure $e) {
                        $stats->incrementFailures();
                        $print->failed(
                            $this->output,
                            $this->error,
                            $e,
                        );

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
        }

        $this->print->end($this->output, $this->error, $stats);
        $coverage?->dump();
    }

    /**
     * @param \Generator<Proof> $proofs
     * @param positive-int $scenariiPerProof
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
        );
    }
}
