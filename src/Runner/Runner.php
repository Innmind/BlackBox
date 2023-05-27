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
    ) {
        $this->random = $random;
        $this->print = $print;
        $this->output = $output;
        $this->error = $error;
        $this->run = $run;
        $this->proofs = $proofs;
        $this->scenariiPerProof = $scenariiPerProof;
    }

    public function __invoke(Stats $stats, Assert $assert): void
    {
        $this->print->start($this->output, $this->error);

        foreach ($this->proofs as $proof) {
            $stats->incrementProofs();
            $print = $this->print->proof(
                $this->output,
                $this->error,
                $proof->name(),
                $proof->tags(),
            );

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
        }

        $this->print->end($this->output, $this->error, $stats);
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
    ): self {
        return new self(
            $random,
            $print,
            $output,
            $error,
            $run,
            $proofs,
            $scenariiPerProof,
        );
    }
}
